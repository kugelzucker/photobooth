<?php
function getBoothButton($label, $icon, $command, $type = 'md' ) {
    $btnClass = "
        w-24 h-24 sm:w-28 sm:h-28 m-2 p-2 flex text-white text-4xl bg-black bg-opacity-50 hover:bg-opacity-75 backdrop-filter backdrop-blur-lg
        border border-solid border-white border-opacity-70 rounded-3xl shadow-lg
        flex items-center justify-center flex-col shrink-0 grow-0 outline-none focus:outline-none active:outline-none
        hover:bg-white hover:text-black hover:bg-opacity-100 transition-all duration-200 ease-in-out
    ";
    $btnClass .= ' ' . $command;

    if( $command == "homebtn" ) {
        $btnClass .= ' !absolute !top-4 !left-4';
    }
    if( $type == "gallery" ) {
        $btnClass .= ' !w-full !h-full absolute rounded-none border-opacity-30 border-dashed m-0 !mb-2 p-0 flex items-center justify-center flex-col !bg-transparent hover:text-white';
    }

    if( $type == "xs" ) {
        $btnClass .= ' !w-20 !h-20 !m-1 !p-1 !text-2xl !rounded-xl';
    }
    
    return(
        '<a href="#" class="'. $btnClass .'">
            <i class="'. $icon .' mb-2"></i> 
            <span class="text-sm whitespace-nowrap" data-i18n="'. $label .'">
                '. $label .'
            </span>
        </a>'
    );
}
?>
