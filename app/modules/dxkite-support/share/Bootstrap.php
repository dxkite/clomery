<?php
namespace dxkite\support;

class Bootstrap
{
    public static function page($template)
    {
        $next =$template->get('page.next');
        $previous = $template->get('page.previous');
        $router=$template->get('page.router');
        $current = $template->get('page.current');
        $max = $template->get('page.max');
        $min = $template->get('page.min');

        echo '<nav aria-label="Page navigation"> <ul class="pagination">';
        if ($previous) {
            echo '<li class="page-item"><a class="page-link" href="'.u($router, array_merge($_GET, ['page'=>$current-1])).'">'.__('上一页') .'</a></li>';
        } else {
            echo '<li class="page-item disabled"><a class="page-link" href="#"> '.__('上一页') .'</a></li>';
        }
        
        if ($max <= 10) {
            for ($i=1;$i<=$max;$i++) {
                echo '<li class="page-item'.($current==$i?' active':'').'"><a class="page-link" href="'.($current==$i?'#':u($router, array_merge($_GET, ['page'=>$i]))) .'">'.$i.'</a></li>';
            }
        } else {
            if ($current <= 8) {
                // 1 2 3 4 5 6 7 8 .. max 
                for ($i=1;$i<= 8;$i++) {
                    echo '<li class="page-item'.($current==$i?' active':'').'"><a class="page-link" href="'.($current==$i?'#':u($router, array_merge($_GET, ['page'=>$i]))) .'">'.$i.'</a></li>';
                }
                echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                echo '<li class="page-item"><a class="page-link" href="'.u($router, array_merge($_GET, ['page'=>$max])) .'">'.$max.'</a></li>';
            }else if ($current >  $max - 8) {
                // 1 ... ~ current ~ max
                echo '<li class="page-item"><a class="page-link" href="'. u($router, array_merge($_GET, ['page'=>1])) .'">1</a></li>';
                echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                for ($i= $max - 7;$i<= $max;$i++) {
                    echo '<li class="page-item'.($current==$i?' active':'').'"><a class="page-link" href="'.($current==$i?'#':u($router, array_merge($_GET, ['page'=>$i]))) .'">'.$i.'</a></li>';
                }
            }else{
                // 1 2 3 ... current-1 current current+1 ... max-1 max 
                for ($i=1;$i<=3;$i++) {
                    echo '<li class="page-item"><a class="page-link" href="'.u($router, array_merge($_GET, ['page'=>$i])) .'">'.$i.'</a></li>';
                }
                echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                for ($i=$current-1;$i<= $current+1 ;$i++) {
                    echo '<li class="page-item'.($current==$i?' active':'').'"><a class="page-link" href="'.($current==$i?'#':u($router, array_merge($_GET, ['page'=>$i]))) .'">'.$i.'</a></li>';
                }
                echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                for ($i=$max-2;$i<=$max;$i++) {
                    echo '<li class="page-item"><a class="page-link" href="'.u($router, array_merge($_GET, ['page'=>$i])) .'">'.$i.'</a></li>';
                }
            }
        }

        if ($next) {
            echo '<li class="page-item"><a class="page-link" href="'.u($router, array_merge($_GET, ['page'=>$current+1])).'">'.__('下一页') .'</a></li>';
        } else {
            echo '<li class="page-item disabled"><a class="page-link" href="#"> '.__('下一页') .'</a></li>';
        }
        echo '</ul></nav>';
    }
}
