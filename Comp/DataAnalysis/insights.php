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
        <?php
          include 'Navbar.php';

          // Determine the current page filename
          $current_page = basename(__FILE__);

          // Output the header
          echo generateHeader($current_page);
        ?>
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
