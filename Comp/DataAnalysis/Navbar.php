<?php
/**
 * Generates the dynamic navigation header.
 * Automatically adjusts relative paths depending on current location.
 *
 * @param string $currentPage The filename of the currently active page (e.g., 'index.php').
 * @param bool $isRoot Whether the file is in the root folder (true for index.php).
 * @return string The complete HTML/CSS string for the header.
 */
function generateHeader($currentPage, $isRoot = false) {
    // Prefix to get back to the project root
    $prefix = $isRoot ? '' : '../../';

    // Define navigation links with paths relative to the project root
    $navLinks = [
        ['text' => 'Dashboard', 'file' => 'index.php'],
        ['text' => 'Sales Overview', 'file' => 'Comp/DataAnalysis/SalesDashboard.php'],
        ['text' => 'Sales Insights', 'file' => 'Comp/DataAnalysis/insights.php'],
        ['text' => 'Vendor Performance', 'file' => 'Comp/DataAnalysis/vendor_overview.php'],
        ['text' => 'Inventory', 'file' => 'Comp/Inventory/inventory_management.php'],
    ];

    $html = '<header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7eff3] px-10 py-3 bg-white">';
    $html .= '  <div class="flex items-center gap-8">';
    
    // --- Brand Name Only (Logo Removed) ---
    $html .= '    <div class="flex items-center gap-4 text-[#0d171b]">';
    $html .= '      <h2 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em]">PIVO</h2>';
    $html .= '    </div>';

    // --- Navigation Links Block ---
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

    // --- Profile Block Only (Search Bar Removed) ---
    $html .= '  <div class="flex items-center justify-end gap-4">';
    $html .= '    <div class="bg-slate-200 aspect-square rounded-full w-10 h-10 flex items-center justify-center font-bold text-[#4c809a]">PH</div>';
    $html .= '  </div>';
    $html .= '</header>';

    return $html;
}
?>