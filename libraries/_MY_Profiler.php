<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Profiler extends CI_Profiler
{
    function __construct()
    {
        parent::CI_Profiler();
    }

    function run()
    {
            $output = <<<ENDJS
<script src="http://code.jquery.com/jquery-latest.js" />
<script type="text/javascript" language="javascript" charset="utf-8">
// < ![CDATA[
    $(document).ready(function() {
        var html = $('#codeigniter_profiler').clone();
        $('#codeigniter_profiler').remove();
        $('#debug').hide().empty().append(html).fadeIn('slow');
    }); 
// ]]>
</script>
ENDJS;
            $output .= "<div id='codeigniter_profiler' style='font-size: 0.7em; clear:both;background-color:#fff;padding:10px;'>";
            $output .= $this->_compile_uri_string();
            $output .= $this->_compile_controller_info();
            $output .= $this->_compile_memory_usage();
            $output .= $this->_compile_benchmarks();
            $output .= $this->_compile_get();
            $output .= $this->_compile_post();
            $output .= $this->_compile_queries();
            $output .= '</div>';
            return $output;
    }
}