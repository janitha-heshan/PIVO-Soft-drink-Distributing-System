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

    <title>Sales</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="stylesheet" href="style.css">
</head>
  <body>
    <div class="relative flex size-full min-h-screen flex-col bg-slate-50 group/design-root overflow-x-hidden" class="j-root">
      <div class="j-layout-container flex h-full grow flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7eff3] px-10 py-3">
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
          <div class="flex flex-1 justify-end gap-8">
            <div class="flex items-center gap-9">
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Dashboard</a>
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Reports</a>
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Customers</a>
              <a class="text-[#0d171b] text-sm font-medium leading-normal" href="#">Products</a>
            </div>
            <button
              class="flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 bg-[#e7eff3] text-[#0d171b] gap-2 text-sm font-bold leading-normal tracking-[0.015em] min-w-0 px-2.5"
            >
              <div class="text-[#0d171b]" data-icon="Bell" data-size="20px" data-weight="regular">
                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                  <path
                    d="M221.8,175.94C216.25,166.38,208,139.33,208,104a80,80,0,1,0-160,0c0,35.34-8.26,62.38-13.81,71.94A16,16,0,0,0,48,200H88.81a40,40,0,0,0,78.38,0H208a16,16,0,0,0,13.8-24.06ZM128,216a24,24,0,0,1-22.62-16h45.24A24,24,0,0,1,128,216ZM48,184c7.7-13.24,16-43.92,16-80a64,64,0,1,1,128,0c0,36.05,8.28,66.73,16,80Z"
                  ></path>
                </svg>
              </div>
            </button>
            <div
              class="bg-center bg-no-repeat aspect-square bg-cover  j-avatar-1 rounded-full j-size-10"
              
            ></div>
          </div>
        </header>
        <div class="px-40 flex flex-1 justify-center py-5">
          <div class="j-layout-content-container flex flex-col max-w-[960px] flex-1">
            <div class="flex flex-wrap justify-between gap-3 p-4"><p class="text-[#0d171b] tracking-light text-[32px] font-bold leading-tight min-w-72">Sales Overview</p></div>
            <div class="flex flex-wrap gap-4 px-4 py-6">
              <div class="flex min-w-72 flex-1 flex-col gap-2 rounded-lg border border-[#cfdfe7] p-6">
                <p class="text-[#0d171b] text-base font-medium leading-normal">Sales by Drink Type</p>
                <p class="text-[#0d171b] tracking-light text-[32px] font-bold leading-tight truncate">12,500</p>
                <div class="flex gap-1">
                  <p class="text-[#4c809a] text-base font-normal leading-normal">Last Month</p>
                  <p class="text-[#078836] text-base font-medium leading-normal">+15%</p>
                </div>
                <div class="grid min-h-[180px] gap-x-4 gap-y-6 grid-cols-[auto_1fr] items-center py-3">
                  <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Coffee</p>
                  <div class="h-full flex-1"><div class="border-[#4c809a] bg-[#e7eff3] border-r-2 h-full"></div></div>
                  <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Tea</p>
                  <div class="h-full flex-1"><div class="border-[#4c809a] bg-[#e7eff3] border-r-2 h-full"></div></div>
                  <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Juice</p>
                  <div class="h-full flex-1"><div class="border-[#4c809a] bg-[#e7eff3] border-r-2 h-full"></div></div>
                  <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Smoothie</p>
                  <div class="h-full flex-1"><div class="border-[#4c809a] bg-[#e7eff3] border-r-2 h-full"></div></div>
                  <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Soda</p>
                  <div class="h-full flex-1"><div class="border-[#4c809a] bg-[#e7eff3] border-r-2 h-full"></div></div>
                </div>
              </div>
              <div class="flex min-w-72 flex-1 flex-col gap-2 rounded-lg border border-[#cfdfe7] p-6">
                <p class="text-[#0d171b] text-base font-medium leading-normal">Sales Prediction (Next 3 Months)</p>
                <p class="text-[#0d171b] tracking-light text-[32px] font-bold leading-tight truncate">15,000</p>
                <div class="flex gap-1">
                  <p class="text-[#4c809a] text-base font-normal leading-normal">Current Month</p>
                  <p class="text-[#078836] text-base font-medium leading-normal">+10%</p>
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
                    <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Month 1</p>
                    <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Month 2</p>
                    <p class="text-[#4c809a] text-[13px] font-bold leading-normal tracking-[0.015em]">Month 3</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
