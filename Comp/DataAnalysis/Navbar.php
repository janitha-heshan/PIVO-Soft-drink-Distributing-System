<?php
/**
 * Generates the dynamic Sales Insights navigation header.
 * Automatically adjusts relative paths depending on current location.
 *
 * @param string $currentPage The filename of the currently active page (e.g., 'index.php').
 * @param bool $isRoot Whether the file is in the root folder (true for index.php).
 * @return string The complete HTML/CSS string for the header.
 */
function generateHeader($currentPage, $isRoot = false) {
    // Set correct relative path prefix based on location
    // If inside Comp/DataAnalysis/, go up two levels to reach root
    $prefix = $isRoot ? 'Comp/DataAnalysis/' : '../../';

    // Define navigation links (paths relative to where this function is called)
    $navLinks = [
        ['text' => 'Dashboard', 'file' => $prefix . 'index.php'],
        ['text' => 'Sales Overview', 'file' => $prefix . 'Comp/DataAnalysis/SalesDashboard.php'],
        ['text' => 'Sales Insights', 'file' => $prefix . 'Comp/DataAnalysis/insights.php'],
        ['text' => 'Sales Overview by Vendor', 'file' => $prefix . 'Comp/DataAnalysis/vendor_overview.php'],
    ];

    $html = '<header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#e7eff3] px-10 py-3">';
    $html .= '  <div class="flex items-center gap-8">';
    $html .= '    <div class="flex items-center gap-4 text-[#0d171b]">';

    // --- Logo/Icon Block ---
    $html .= '      <div class="j-size-4">';
    $html .= '        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">';
    $html .= '          <path fill-rule="evenodd" clip-rule="evenodd" d="M39.475 21.6262C40.358 21.4363 40.6863 21.5589 40.7581 21.5934C40.7876 21.655 40.8547 21.857 40.8082 22.3336C40.7408 23.0255 40.4502 24.0046 39.8572 25.2301C38.6799 27.6631 36.5085 30.6631 33.5858 33.5858C30.6631 36.5085 27.6632 38.6799 25.2301 39.8572C24.0046 40.4502 23.0255 40.7407 22.3336 40.8082C21.8571 40.8547 21.6551 40.7875 21.5934 40.7581C21.5589 40.6863 21.4363 40.358 21.6262 39.475C21.8562 38.4054 22.4689 36.9657 23.5038 35.2817C24.7575 33.2417 26.5497 30.9744 28.7621 28.762C30.9744 26.5497 33.2417 24.7574 35.2817 23.5037C36.9657 22.4689 38.4054 21.8562 39.475 21.6262ZM4.41189 29.2403L18.7597 43.5881C19.8813 44.7097 21.4027 44.9179 22.7217 44.7893C24.0585 44.659 25.5148 44.1631 26.9723 43.4579C29.9052 42.0387 33.2618 39.5667 36.4142 36.4142C39.5667 33.2618 42.0387 29.9052 43.4579 26.9723C44.1631 25.5148 44.659 24.0585 44.7893 22.7217C44.9179 21.4027 44.7097 19.8813 43.5881 18.7597L29.2403 4.41187C27.8527 3.02428 25.8765 3.02573 24.2861 3.36776C22.6081 3.72863 20.7334 4.58419 18.8396 5.74801C16.4978 7.18716 13.9881 9.18353 11.5858 11.5858C9.18354 13.988 7.18717 16.4978 5.74802 18.8396C4.58421 20.7334 3.72865 22.6081 3.36778 24.2861C3.02574 25.8765 3.02429 27.8527 4.41189 29.2403Z" fill="currentColor"></path>';
    $html .= '        </svg>';
    $html .= '      </div>';

    // --- Brand Name ---
    $html .= '      <h2 class="text-[#0d171b] text-lg font-bold leading-tight tracking-[-0.015em]">PIVO</h2>';
    $html .= '    </div>';

    // --- Navigation Links Block ---
    $html .= '    <div class="flex items-center gap-9">';
    foreach ($navLinks as $link) {
        // Get filename only to match active page
        $isActive = (basename($link['file']) === $currentPage);
        $class = $isActive 
            ? 'text-[#0d171b] text-sm font-bold leading-normal border-b-2 border-solid border-[#0d171b]' 
            : 'text-[#4c809a] text-sm font-medium leading-normal';
        $html .= '      <a class="' . $class . '" href="' . $link['file'] . '">' . $link['text'] . '</a>';
    }
    $html .= '    </div>';
    $html .= '  </div>';

    // --- Search & Profile Block ---
    $html .= '  <div class="flex flex-1 justify-end gap-8">';
    $html .= '    <label class="flex flex-col min-w-40 !h-10 max-w-64">';
    $html .= '      <div class="flex w-full flex-1 items-stretch rounded-lg h-full">';
    $html .= '        <div class="text-[#4c809a] flex border-none bg-[#e7eff3] items-center justify-center pl-4 rounded-l-lg border-r-0">';
    $html .= '          <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">';
    $html .= '            <path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path>';
    $html .= '          </svg>';
    $html .= '        </div>';
    $html .= '        <input placeholder="Search" class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#0d171b] focus:outline-0 border-none bg-[#e7eff3] h-full placeholder:text-[#4c809a] px-4 rounded-l-none pl-2 text-base font-normal leading-normal"/>';
    $html .= '      </div>';
    $html .= '    </label>';
    $html .= '    <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full j-size-10"></div>';
    $html .= '  </div>';
    $html .= '</header>';

    return $html;
}
?>
