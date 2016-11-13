<?php
/**
 * Regenerate all cached images that have been created as the result of a manipulation method being called on a
 * {@link Image} object
 *
 * @package framework
 * @subpackage filesystem
 */
class ResizeImagesTask extends BuildTask {

    protected $title = 'Resize Images Task';

    protected $description = 'Resize all images exceeding a specific width & height (1600 x 1200)';

    private static $max_width = 1023;

    private static $max_height = 767;

    /**
     * Check that the user has appropriate permissions to execute this task
     */
    public function init() {
        if(!Director::is_cli() && !Director::isDev() && !Permission::check('ADMIN')) {
            return Security::permissionFailure();
        }

        parent::init();
    }

    /**
     * Actually regenerate all the images
     */
    public function run($request) {
        $processedImages   = 0;
        $resizedImages = 0;

        if($images = DataObject::get('Image')) foreach($images as $image) {
            SS_Log::log('image = '.$image->getFullPath(),SS_Log::WARN);
            $extension = $image->getExtension();

            if (
                $image->getHeight() > self::$max_height
                || $image->getWidth() > self::$max_width && preg_match('/jpe?g/i', $extension)
            ) {
                $this->Scale($image);
                $resizedImages++;

            }

            $processedImages++;
        }

        echo "Resized $resizedImages images from $processedImages Image objects stored in the Database.";
    }

    public function Scale($image)
    {

            $original = $image->getFullPath();
            $extension = $image->getExtension();
            /* temporary location for image manipulation */
            $resampled = TEMP_FOLDER .'/resampled-' . mt_rand(100000, 999999) . '.' . $extension;

            $gd = new GD($original);

            /* Backwards compatibility with SilverStripe 3.0 */
            $image_loaded = (method_exists('GD', 'hasImageResource')) ? $gd->hasImageResource() : $gd->hasGD();

            if ($image_loaded) {

                /* Clone original */
                $transformed = $gd;

                /* Resize to max values */
                if (
                    $transformed && (
                        $transformed->getWidth() > self::$max_width
                        || $transformed->getHeight() > self::$max_height
                    )
                ) {
                    $transformed = $transformed->resizeRatio(self::$max_width, self::$max_height);
                }

                /* Write to tmp file and then overwrite original */
                if ($transformed) {
                    $transformed->writeTo($resampled);
                    file_put_contents($original, file_get_contents($resampled));
                    unlink($resampled);
                }
            }

    }

}
