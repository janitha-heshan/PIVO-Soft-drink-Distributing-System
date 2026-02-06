<?php
/**
 * Updated to include a Profile Dropdown with Logout functionality.
 */
function generateHeader($currentPage, $isRoot = false)
{
    // Prefix to get back to the project root
    $prefix = $isRoot ? '' : '../../';

    $navLinks = [
        ['text' => 'Dashboard', 'file' => 'admin/dashboard.php'],
        ['text' => 'Sales Overview', 'file' => 'Comp/DataAnalysis/SalesDashboard.php'],
        ['text' => 'Sales Insights', 'file' => 'Comp/DataAnalysis/insights.php'],
        ['text' => 'Vendor Performance', 'file' => 'Comp/DataAnalysis/vendor_overview.php'],
        ['text' => 'Inventory', 'file' => 'manager/inventory.php'],
    ];

    $html = '<header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7eff3] px-10 py-3 bg-white relative">';
    $html .= '  <div class="flex items-center gap-8">';

    // --- Brand ---
    $html .= '    <div class="flex items-center gap-4 text-[#0d171b]">';
    $html .= '      <h2 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em]">PIVO</h2>';
    $html .= '    </div>';

    // --- Navigation Links ---
    $html .= '    <div class="flex items-center gap-9">';
    foreach ($navLinks as $link) {
        $fullPath = $prefix . $link['file'];
        $isActive = (basename($link['file']) === $currentPage);
        $class = $isActive
            ? 'text-[#0d171b] text-sm font-bold leading-normal border-b-2 border-solid border-[#0d171b]'
            : 'text-[#4c809a] text-sm font-medium leading-normal hover:text-[#0d171b]';
        $html .= '      <a class="' . $class . '" href="' . $fullPath . '">' . $link['text'] . '</a>';
    }
    $html .= '    </div>';
    $html .= '  </div>';

    // --- Profile Block with Dropdown ---
    $html .= '  <div class="flex items-center justify-end gap-4 relative">';
    $html .= '    <button onclick="toggleDropdown()" class="bg-slate-200 aspect-square rounded-full w-10 h-10 flex items-center justify-center font-bold text-[#4c809a] cursor-pointer hover:bg-slate-300 transition-colors focus:outline-none">PH</button>';

    // Hidden Dropdown Menu
    $html .= '    <div id="profileDropdown" class="hidden absolute right-0 top-12 w-48 bg-white border border-gray-200 rounded-md shadow-lg py-1 z-50">';
    $html .= '      <a href="' . $prefix . 'logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 font-medium italic">Logout</a>';
    $html .= '    </div>';
    $html .= '  </div>';

    // --- Simple JS Toggle ---
    $html .= '  <script>
        function toggleDropdown() {
            const menu = document.getElementById("profileDropdown");
            menu.classList.toggle("hidden");
        }
        // Close dropdown if clicked outside
        window.addEventListener("click", function(e) {
            const menu = document.getElementById("profileDropdown");
            const btn = menu.previousElementSibling;
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add("hidden");
            }
        });
    </script>';

    $html .= '</header>';

    return $html;
}
?>