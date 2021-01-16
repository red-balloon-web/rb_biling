<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Not Authorised' );
} 

if (!in_array('administrator',  wp_get_current_user()->roles)) {
    die( 'Not Authorised' );
} ?>

<?php get_header(); ?>
<div class="rb-nav-pusher"></div>

<div class="rb-section-container">
    <div class="rb-container">
        <?php //echo do_shortcode( ' [rb-simple-blog page="archive"] ' ); 
        //echo do_shortcode( ' [rb-timeline-blog] ' ); ?>

        <!-- GRID BLOG --> 
        <div class="rb-grid-blog">
            <div class="rb-grid-blog__main">
                <?php
                if ( have_posts() ) : 
                    while ( have_posts() ) : the_post(); ?>

                    <div class="archive-item">
                        
                        <a href="<?php echo get_the_permalink(); ?>">
                            <div class="post-image">
                                <?php the_post_thumbnail( 'thumbnail' ); ?>
                                <div class="datebox"><?php echo get_the_date('d') . ' ' . get_the_date('M') . ' ' . get_the_date('Y'); ?></div>
                            </div>
                        </a>

                        <div class="metabox rbveq rbveq--blogmeta rbveq-breakpoint--992">
                            <p class="meta">By <?php echo get_the_author(); ?></p>
                            <p class="meta">
                            <?php if (get_comments_number()) {
                                if (get_comments_number() === '1') {
                                    echo ('<i class="fas fa-comments"></i> '. get_comments_number() . ' comment');
                                } else {
                                    echo ('<i class="fas fa-comments"></i> '. get_comments_number() . ' comments');
                                }
                            }
                            ?>
                            <p class="meta">
                                <?php
                                    $categorylist = get_the_category();
                                    $isfirst = true;
                                    foreach ($categorylist as $category) {
                                        if (!$isfirst) {
                                            echo ", ";
                                        } else {
                                            $isfirst = false;
                                        }
                                        echo $category->name;
                                    }
                                ?>
                            </p>
                        </div>

                        <hr>

                        <div class="contentbox rbveq rbveq--contentbox">
                            <h2><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                            <p><?php echo get_the_excerpt(); ?></p>
                        </div>
                    </div>
                <?php endwhile; 
                endif; ?>
            </div>

            <div class="rb-simple-blog__sidebar">
                <?php dynamic_sidebar('blog-sidebar'); ?>
            </div>
        </div>
        
    </div>
</div>



<?php get_footer(); ?>
