<?php
/**
 * @package Case-Themes
 */
$subtitle_404 = frameflow()->get_theme_opt('subtitle_404');
$title_404 = frameflow()->get_theme_opt('title_404');
$des_404 = frameflow()->get_theme_opt('des_404');
$button_404 = frameflow()->get_theme_opt('button_404');
get_header(); ?>
<div class="wrap-content-404 container" >
            <div class="pxl-error-404 wow fadeInUp" data-text="404">
                <div class="pxl-text-banner--left pxl-animated" bis_skin_checked="1">
					<span class="pxl-text-banner--text">
						Event Facts					</span>
					<svg xmlns="http://www.w3.org/2000/svg" width="145" height="42" viewBox="0 0 145 42" fill="none">
						<path d="M144.007 0.5C141.595 3.82716 138.195 9.60665 137.339 17.2812C136.039 28.9185 141.379 37.7939 144.018 41.5H1.10254C3.80616 38.325 9.95703 29.9149 8.9707 18.46C8.22145 9.75402 3.71064 3.5107 1.1123 0.5H144.007Z" fill="var(--six-color)" stroke="var(--secondary-color)"></path>
					</svg>
				</div>
                <div class="pxl-text-banner--right pxl-animated" bis_skin_checked="1">
					<span class="pxl-text-banner--text">
						Error			</span>
					<svg xmlns="http://www.w3.org/2000/svg" width="135" height="42" viewBox="0 0 135 42" fill="none">
						<path d="M134.5 41.4609C124.426 40.723 114.353 39.9867 104.279 39.249C94.0272 38.4983 83.7749 37.7472 73.5225 36.9961C69.4537 36.6983 65.353 36.6979 61.2842 36.9971C51.0639 37.7475 40.844 38.4983 30.624 39.249C20.5828 39.9866 10.5415 40.7236 0.5 41.4609V0.547852L55.7764 5.62402C62.8344 6.27182 69.9782 6.28228 77.0391 5.65527C86.7063 4.79624 96.3744 3.9365 106.042 3.07715C115.528 2.23392 125.014 1.38979 134.5 0.546875V41.4609Z" fill="var(--primary-color)" stroke="var(--secondary-color)"></path>
					</svg>
				</div>
               404
            </div>
            <p class="pxl-error-description wow fadeInUp">
                <?php if (!empty($des_404)) {
                    echo pxl_print_html($des_404);
                } else{
                    echo esc_html__('Looks like here is something missing!', 'frameflow');
                } ?>
            </p>
            <a class="btn-sm" href="<?php echo esc_url(home_url('/')); ?>" >
                <span>
                    <?php if (!empty($button_404)) {
                        echo pxl_print_html($button_404);
                    } else{
                       echo esc_html__('Go back home', 'frameflow'); 
                   } ?>
               </span>
               <span class="btn--icon">
                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none">
                    <path d="M9.6 12.7999C9.39526 12.7999 9.19053 12.7219 9.03432 12.5657C8.7219 12.2532 8.7219 11.7467 9.03432 11.4343L13.2686 7.19999H0.800009C0.358159 7.19999 0 6.8418 0 6.39998C0 5.95813 0.358159 5.59997 0.800009 5.59997H13.2686L9.03432 1.36567C8.7219 1.05326 8.7219 0.546725 9.03432 0.234311C9.3467 -0.0781035 9.8533 -0.0781035 10.1657 0.234311L15.7657 5.8343L15.7674 5.83604C15.7677 5.83632 15.768 5.83667 15.7683 5.83695C15.7686 5.83723 15.7688 5.83751 15.7691 5.83778C15.7695 5.8382 15.7699 5.83862 15.7703 5.83904C15.7705 5.83918 15.7706 5.83932 15.7708 5.83949C15.7713 5.84005 15.7718 5.84057 15.7724 5.84109L15.7724 5.84116C15.8443 5.91483 15.8992 5.9989 15.937 6.08847C15.9371 6.08872 15.9372 6.089 15.9373 6.08924C15.9374 6.08952 15.9376 6.08983 15.9377 6.09011C15.9778 6.18543 15.9999 6.29015 15.9999 6.40002C15.9999 6.50989 15.9778 6.61461 15.9377 6.70993C15.9376 6.71017 15.9374 6.71052 15.9373 6.7108C15.9372 6.71104 15.9371 6.71128 15.937 6.71153C15.8992 6.80114 15.8443 6.88521 15.7724 6.95888L15.7724 6.95891C15.7718 6.95947 15.7713 6.95999 15.7708 6.96051C15.7707 6.96065 15.7705 6.96079 15.7703 6.96096C15.7699 6.96142 15.7695 6.9618 15.7691 6.96225C15.7688 6.9625 15.7686 6.96281 15.7683 6.96305C15.768 6.96333 15.7677 6.96368 15.7674 6.96396C15.7668 6.96455 15.7662 6.96514 15.7657 6.9657L10.1657 12.5657C10.0095 12.7219 9.80474 12.7999 9.6 12.7999Z" fill="#1A1A1A"/>
                    </svg>
               </span>
           </a>
</div>
<?php get_footer();
