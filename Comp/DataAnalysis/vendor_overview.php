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

    <title>Stitch Design</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="stylesheet" href="style.css">
</head>
  <body>
    <div
      class="relative flex size-full min-h-screen flex-col bg-slate-50 group/design-root overflow-x-hidden"
      class="j-root"
    >
      <div class="j-layout-container flex h-full grow flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7eff3] px-10 py-3">
          <div class="flex items-center gap-8">
            <div class="flex items-center gap-4 text-[#0d171b]">
              <div class="j-size-4">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g clip-path="url(#clip0_6_330)">
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M24 0.757355L47.2426 24L24 47.2426L0.757355 24L24 0.757355ZM21 35.7574V12.2426L9.24264 24L21 35.7574Z"
                      fill="currentColor"
                    ></path>
                  </g>
                  <defs>
                    <clipPath id="clip0_6_330"><rect width="48" height="48" fill="white"></rect></clipPath>
                  </defs>
                </svg>
              </div>
              <h2 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em]">Sales Analytics</h2>
            </div>
            <div class="flex items-center gap-9">
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Dashboard</a>
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Reports</a>
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Analytics</a>
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Settings</a>
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
              class="bg-center bg-no-repeat aspect-square bg-cover  j-avatar-1 rounded-full j-size-10"
              
            ></div>
          </div>
        </header>
        <div class="px-40 flex flex-1 justify-center py-5">
          <div class="j-layout-content-container flex flex-col max-w-[960px] flex-1">
            <div class="flex flex-wrap justify-between gap-3 p-4">
              <p class="text-[#0d171b] tracking-light text-[32px] font-bold leading-tight min-w-72">Sales Overview by Vendor</p>
            </div>
            <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
              <label class="flex flex-col min-w-40 flex-1">
                <select
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#0d171b] focus:outline-0 focus:ring-0 border border-[#cfdfe7] bg-slate-50 focus:border-[#cfdfe7] h-14 bg-[image:--select-button-svg] placeholder:text-[#4c809a] p-[15px] text-base font-normal leading-normal"
                >
                  <option value="one"></option>
                  <option value="two">two</option>
                  <option value="three">three</option>
                </select>
              </label>
            </div>
            <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
              <label class="flex flex-col min-w-40 flex-1">
                <select
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#0d171b] focus:outline-0 focus:ring-0 border border-[#cfdfe7] bg-slate-50 focus:border-[#cfdfe7] h-14 bg-[image:--select-button-svg] placeholder:text-[#4c809a] p-[15px] text-base font-normal leading-normal"
                >
                  <option value="one"></option>
                  <option value="two">two</option>
                  <option value="three">three</option>
                </select>
              </label>
            </div>
            <div class="flex flex-wrap gap-4 px-4 py-6">
              <div class="flex min-w-72 flex-1 flex-col gap-2 rounded-lg border border-[#cfdfe7] p-6">
                <p class="text-[#0d171b] text-base font-medium leading-normal">Sales Prediction</p>
                <p class="text-[#0d171b] tracking-light text-[32px] font-bold leading-tight truncate">$250K</p>
                <div class="flex gap-1">
                  <p class="text-[#4c809a] text-base font-normal leading-normal">Next 3 Months</p>
                  <p class="text-[#078836] text-base font-medium leading-normal">+15%</p>
                </div>
                <div class="flex min-h-[180px] flex-1 flex-col gap-8 py-4">
                  <svg width="100%" height="148" viewBox="-3 0 478 150" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                    <path
                      d="M0 109C18.1538 109 18.1538 21 36.3077 21C54.4615 21 54.4615 41 72.6154 41C90.7692 41 90.7692 93 108.923 93C127.077 93 127.077 33 145.231 33C163.385 33 163.385 101 181.538 101C199.692 101 199.692 61 217.846 61C236 61 236 45 254.154 45C272.308 45 272.308 121 290.462 121C308.615 121 308.615 149 326.769 149C344.923 149 344.923 1 363.077 1C381.231 1 381.231 81 399.385 81C417.538 81 417.538 129 435.692 129C453.846 129 453.846 25 472 25V149H326.769H0V109Z"
                      fill="url(#paint0_linear_1131_5935)"
                    ></path>
                    <path
                      d="M0 109C18.1538 109 18.1538 21 36.3077 21C54.4615 21 54.4615 41 72.6154 41C90.7692 41 90.7692 93 108.923 93C127.077 93 127.077 33 145.231 33C163.385 33 163.385 101 181.538 101C199.692 101 199.692 61 217.846 61C236 61 236 45 254.154 45C272.308 45 272.308 121 290.462 121C308.615 121 308.615 149 326.769 149C344.923 149 344.923 1 363.077 1C381.231 1 381.231 81 399.385 81C417.538 81 417.538 129 435.692 129C453.846 129 453.846 25 472 25"
                      stroke="#4c809a"
                      stroke-width="3"
                      stroke-linecap="round"
                    ></path>
                    <defs>
                      <linearGradient id="paint0_linear_1131_5935" x1="236" y1="1" x2="236" y2="149" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#e7eff3"></stop>
                        <stop offset="1" stop-color="#e7eff3" stop-opacity="0"></stop>
                      </linearGradient>
                    </defs>
                  </svg>
                  <div class="flex justify-around">
                    <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Jan</p>
                    <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Feb</p>
                    <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Mar</p>
                    <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Apr</p>
                    <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">May</p>
                    <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Jun</p>
                  </div>
                </div>
              </div>
            </div>
            <h2 class="text-[#0d171b] text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Sales Data</h2>
            <div class="px-4 py-3 @container">
              <div class="flex overflow-hidden rounded-lg border border-[#cfdfe7] bg-slate-50">
                <table class="flex-1">
                  <thead>
                    <tr class="bg-slate-50">
                      <th class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-120 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">Vendor</th>
                      <th class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-240 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">
                        Distributor
                      </th>
                      <th class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-360 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">Drink</th>
                      <th class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-480 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">
                        Units Sold
                      </th>
                      <th class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-600 px-4 py-3 text-left text-[#0d171b] w-[400px] text-sm font-medium leading-normal">Income</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">Vendor A</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">
                        Distributor X
                      </td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">
                        Sparkling Water
                      </td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-480 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">1200</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-600 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$15,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">Vendor B</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">
                        Distributor Y
                      </td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">
                        Energy Drink
                      </td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-480 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">1500</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-600 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$20,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">Vendor A</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">
                        Distributor X
                      </td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">Iced Tea</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-480 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">800</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-600 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$10,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">Vendor C</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">
                        Distributor Z
                      </td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">
                        Fruit Juice
                      </td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-480 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">1000</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-600 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$12,000</td>
                    </tr>
                    <tr class="border-t border-t-[#cfdfe7]">
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-120 h-[72px] px-4 py-2 w-[400px] text-[#0d171b] text-sm font-normal leading-normal">Vendor B</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-240 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">
                        Distributor Y
                      </td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-360 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">Smoothie</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-480 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">1100</td>
                      <td class="j-table-8d03c77b-5d5a-4da9-bda1-eac5120bae42-column-600 h-[72px] px-4 py-2 w-[400px] text-[#4c809a] text-sm font-normal leading-normal">$14,000</td>
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
