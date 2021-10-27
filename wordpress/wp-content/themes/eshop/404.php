<?php
// dd($categories);
// Header
get_header()
?>
<main class="my-4">
    <section>
        <div class="eshop-container flex flex-col items-center justify-center">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/404.svg" class="image-404 inline-block" />
            <div class="mb-4">
                Maaf, Halaman yang anda cari tidak kami temukan.
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>

<style>
    .image-404 {
        height: 80vh;
    }
    @media only screen and (max-width: 600px) {
        .image-404 {
            height: auto;
        }
    }
</style>