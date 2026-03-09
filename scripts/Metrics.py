import mysql.connector
import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
# --- ADDED IMPORTS FOR EVALUATION ---
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
# ------------------------------------
import joblib
import os
from datetime import datetime, timedelta

# DATABASE CONNECTION
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'pivo_holdings_db'
}

MODEL_DIR = 'saved_models'
if not os.path.exists(MODEL_DIR):
    os.makedirs(MODEL_DIR)

def update_predictions():
    conn = None
    cursor = None
    try:
        conn = mysql.connector.connect(**db_config)
        
        # 1. FETCH HISTORICAL DATA
        query = """
            SELECT product_id, YEAR(order_date) as year, MONTH(order_date) as month, SUM(quantity) as total_qty
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.order_id
            WHERE o.delivery_status != 'Cancelled'
            GROUP BY product_id, year, month
        """
        df = pd.read_sql(query, conn)
        cursor = conn.cursor()

        if df.empty:
            print("No historical data found.")
            return

        product_ids = df['product_id'].unique()
        cursor.execute("DELETE FROM sales_predictions")

        print(f"\n{'='*60}")
        print(f"{'PRODUCT ID':<12} | {'R2 SCORE':<10} | {'MAE':<8} | {'RMSE':<8}")
        print(f"{'-'*60}")

        for pid in product_ids:
            model_path = os.path.join(MODEL_DIR, f'model_prod_{pid}.pkl')
            product_data = df[df['product_id'] == pid]
            
            X = product_data[['year', 'month']]
            y = product_data['total_qty']

            # 2. TRAINING LOGIC
            if len(product_data) >= 2:
                model = LinearRegression()
                model.fit(X, y)
                joblib.dump(model, model_path)
                
                # --- CALCULATION OF PARAMETERS ---
                # We predict on the same X to see how well it fits the history
                y_pred = model.predict(X)
                
                r2 = r2_score(y, y_pred)
                mae = mean_absolute_error(y, y_pred)
                mse = mean_squared_error(y, y_pred)
                rmse = np.sqrt(mse)
                
                # Print metrics in a clean table format
                print(f"{pid:<12} | {r2:>10.4f} | {mae:>8.2f} | {rmse:>8.2f}")
                
                # Optional: Print raw coefficients (The "Slope" of demand)
                # print(f"   -> Growth Trend: {model.coef_[0]:.2f} units/year")

            else:
                print(f"{pid:<12} | Insufficient data to calculate accuracy.")
                continue

            # 3. GENERATE FUTURE PREDICTIONS
            last_date = datetime(2026, 1, 17)
            for i in range(1, 4):
                future_date = last_date + timedelta(days=31 * i)
                pred_input = pd.DataFrame([[future_date.year, future_date.month]], columns=['year', 'month'])
                prediction = max(0, int(model.predict(pred_input)[0]))

                insert_sql = "INSERT INTO sales_predictions (product_id, prediction_date, predicted_demand) VALUES (%s, %s, %s)"
                cursor.execute(insert_sql, (int(pid), future_date.strftime('%Y-%m-01'), prediction))

        conn.commit()
        print(f"{'='*60}")
        print("Predictions updated successfully.")

    except Exception as e:
        print(f"Error: {e}")
    finally:
        if conn and conn.is_connected():
            if cursor:
                cursor.close()
            conn.close()

if __name__ == "__main__":
    update_predictions()