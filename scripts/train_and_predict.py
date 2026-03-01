import mysql.connector
import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
import joblib # Library for saving/loading models
import os
from datetime import datetime, timedelta

# DATABASE CONNECTION
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'pivo_holdings_db'
}

# Ensure a directory exists to store the saved models
MODEL_DIR = 'saved_models'
if not os.path.exists(MODEL_DIR):
    os.makedirs(MODEL_DIR)

def update_predictions():
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        # 1. FETCH HISTORICAL DATA
        query = """
            SELECT product_id, YEAR(order_date) as year, MONTH(order_date) as month, SUM(quantity) as total_qty
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.delivery_status != 'Cancelled'
            GROUP BY product_id, year, month
        """
        df = pd.read_sql(query, conn)

        if df.empty:
            print("No historical data found.")
            return

        product_ids = df['product_id'].unique()
        cursor.execute("DELETE FROM sales_predictions")

        for pid in product_ids:
            model_path = os.path.join(MODEL_DIR, f'model_prod_{pid}.pkl')
            product_data = df[df['product_id'] == pid]
            
            X = product_data[['year', 'month']]
            y = product_data['total_qty']

            # 2. RETRAIN OR LOAD LOGIC
            # If we have more than 3 data points, it's worth retraining
            if len(product_data) >= 2:
                print(f"Retraining model for Product {pid}...")
                model = LinearRegression()
                model.fit(X, y)
                # SAVE the model to a file
                joblib.dump(model, model_path)
            else:
                # If data is too small but a model exists, LOAD it
                if os.path.exists(model_path):
                    print(f"Using saved model for Product {pid}...")
                    model = joblib.load(model_path)
                else:
                    # Fallback if no model and no data
                    print(f"Insufficient data for Product {pid}, skipping.")
                    continue

            # 3. GENERATE PREDICTIONS
            last_date = datetime(2026, 1, 17)
            for i in range(1, 4):
                future_date = last_date + timedelta(days=31 * i)
                pred_input = np.array([[future_date.year, future_date.month]])
                
                prediction = max(0, int(model.predict(pred_input)[0]))

                insert_sql = """
                    INSERT INTO sales_predictions (product_id, prediction_date, predicted_demand)
                    VALUES (%s, %s, %s)
                """
                cursor.execute(insert_sql, (int(pid), future_date.strftime('%Y-%m-01'), prediction))

        conn.commit()
        print("Predictions updated successfully.")

    except Exception as e:
        print(f"Error: {e}")
    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

if __name__ == "__main__":
    update_predictions()