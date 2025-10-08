<?php header("Content-Type: text/html; charset=utf-8"); ?>
<html>
  <head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link
      rel="stylesheet"
      as="style"
      onload="this.rel='stylesheet'"
      href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Work+Sans%3Awght%40400%3B500%3B700%3B900"
    />

    <title>Insights</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="stylesheet" href="style.css">
</head>
  <body>
    <div class="relative flex size-full min-h-screen flex-col bg-slate-50 group/design-root overflow-x-hidden" class="j-root">
      <div class="j-layout-container flex h-full grow flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7eff3] px-10 py-3">
          <div class="flex items-center gap-8">
            <div class="flex items-center gap-4 text-[#0d171b]">
              <div class="j-size-4">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M39.475 21.6262C40.358 21.4363 40.6863 21.5589 40.7581 21.5934C40.7876 21.655 40.8547 21.857 40.8082 22.3336C40.7408 23.0255 40.4502 24.0046 39.8572 25.2301C38.6799 27.6631 36.5085 30.6631 33.5858 33.5858C30.6631 36.5085 27.6632 38.6799 25.2301 39.8572C24.0046 40.4502 23.0255 40.7407 22.3336 40.8082C21.8571 40.8547 21.6551 40.7875 21.5934 40.7581C21.5589 40.6863 21.4363 40.358 21.6262 39.475C21.8562 38.4054 22.4689 36.9657 23.5038 35.2817C24.7575 33.2417 26.5497 30.9744 28.7621 28.762C30.9744 26.5497 33.2417 24.7574 35.2817 23.5037C36.9657 22.4689 38.4054 21.8562 39.475 21.6262ZM4.41189 29.2403L18.7597 43.5881C19.8813 44.7097 21.4027 44.9179 22.7217 44.7893C24.0585 44.659 25.5148 44.1631 26.9723 43.4579C29.9052 42.0387 33.2618 39.5667 36.4142 36.4142C39.5667 33.2618 42.0387 29.9052 43.4579 26.9723C44.1631 25.5148 44.659 24.0585 44.7893 22.7217C44.9179 21.4027 44.7097 19.8813 43.5881 18.7597L29.2403 4.41187C27.8527 3.02428 25.8765 3.02573 24.2861 3.36776C22.6081 3.72863 20.7334 4.58419 18.8396 5.74801C16.4978 7.18716 13.9881 9.18353 11.5858 11.5858C9.18354 13.988 7.18717 16.4978 5.74802 18.8396C4.58421 20.7334 3.72865 22.6081 3.36778 24.2861C3.02574 25.8765 3.02429 27.8527 4.41189 29.2403Z"
                    fill="currentColor"
                  ></path>
                </svg>
              </div>
              <h2 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em]">Sales Insights</h2>
            </div>
            <div class="flex items-center gap-9">
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Dashboard</a>
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Reports</a>
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Customers</a>
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Products</a>
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Vendors</a>
            </div>
          </div>
          <div class="flex flex-1 justify-end gap-8">
            <label class="flex flex-col min-w-40 !h-10 max-w-64">
              <div class="flex w-full flex-1 items-stretch rounded-lg h-full">
                <div
                  class="text-[#4c809a] flex border-none bg-[#e7eff3] items-center justify-center pl-4 rounded-l-lg border-r-0"
                  data-icon="MagnifyingGlass"
                  data-size="24px"
                  data-weight="regular"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                    <path
                      d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"
                    ></path>
                  </svg>
                </div>
                <input
                  placeholder="Search"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#0d171b] focus:outline-0 focus:ring-0 border-none bg-[#e7eff3] focus:border-none h-full placeholder:text-[#4c809a] px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
                  value=""
                />
              </div>
            </label>
            <div
              class="bg-center bg-no-repeat aspect-square bg-cover  j-avatar-1  j-avatar-2  j-avatar-3 rounded-full j-size-10"
              
            ></div>
          </div>
        </header>
        <div class="px-40 flex flex-1 justify-center py-5">
          <div class="j-layout-content-container flex flex-col max-w-[960px] flex-1">
            <div class="flex flex-wrap justify-between gap-3 p-4">
              <div class="flex min-w-72 flex-col gap-3">
                <p class="text-[#0d171b] tracking-light text-[32px] font-bold leading-tight">Sales Insights</p>
                <p class="text-[#4c809a] text-sm font-normal leading-normal">Analyze sales data to identify trends and opportunities.</p>
              </div>
            </div>
            <h3 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">Frequently Bought Together</h3>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(158px,1fr))] gap-3 p-4">
              <div class="flex flex-1 gap-3 rounded-lg border border-[#cfdfe7] bg-slate-50 p-4 items-center">
                <div
                  class="bg-center bg-no-repeat aspect-square bg-cover rounded-lg w-10 shrink-0"
                  
                ></div>
                <h2 class="text-[#0d171b] text-base font-bold leading-tight">Pasta</h2>
              </div>
              <div class="flex flex-1 gap-3 rounded-lg border border-[#cfdfe7] bg-slate-50 p-4 items-center">
                <div
                  class="bg-center bg-no-repeat aspect-square bg-cover rounded-lg w-10 shrink-0"
                  
                ></div>
                <h2 class="text-[#0d171b] text-base font-bold leading-tight">Sauce</h2>
              </div>
            </div>
            <h3 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">Top Selling Items</h3>
            <div class="px-4 py-3 @container">
              <div class="flex overflow-hidden rounded-lg border border-[#cfdfe7] bg-slate-50">
                <table class="flex-1">
                  <thead>
                    <tr class="bg-slate-50">
                      <th class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-120 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">Item</th>
                      <th class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-240 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">
                        Quantity Sold
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Organic Apples
                      </td>
                      <td class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">1200</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Whole Wheat Bread
                      </td>
                      <td class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">1150</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Free-Range Eggs
                      </td>
                      <td class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">1100</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Almond Milk
                      </td>
                      <td class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">1050</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">Avocados</td>
                      <td class="j-table-f1b417a4-bddd-4441-961e-5969001f5486-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">1000</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
            </div>
            <h3 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">Highest Income Items</h3>
            <div class="px-4 py-3 @container">
              <div class="flex overflow-hidden rounded-lg border border-[#cfdfe7] bg-slate-50">
                <table class="flex-1">
                  <thead>
                    <tr class="bg-slate-50">
                      <th class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">Item</th>
                      <th class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">
                        Income Generated
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Organic Apples
                      </td>
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$6,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Whole Wheat Bread
                      </td>
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$5,750</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Free-Range Eggs
                      </td>
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$5,500</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Almond Milk
                      </td>
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$5,250</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">Avocados</td>
                      <td class="j-table-9e2cc523-ec92-47bf-9861-47fc055a0074-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$5,000</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
            </div>
            <h3 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">Vendors by Income Type</h3>
            <div class="px-4 py-3 @container">
              <div class="flex overflow-hidden rounded-lg border border-[#cfdfe7] bg-slate-50">
                <table class="flex-1">
                  <thead>
                    <tr class="bg-slate-50">
                      <th class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">Vendor</th>
                      <th class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">
                        Income Type
                      </th>
                      <th class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">
                        Income Amount
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Green Valley Farms
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">Produce</td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$15,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Sunrise Bakery
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">Bakery</td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$12,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Happy Hens
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">Dairy</td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$10,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Nutty Delights
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">
                        Beverages
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$8,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">
                        Creamy Greens
                      </td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">Produce</td>
                      <td class="j-table-e890d7ff-fadd-4f5e-830f-1d23259c1b70-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$7,500</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
