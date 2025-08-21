<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ImageConvertToWebp extends Command
{
    /**
     * Target extention
     *
     * @var str
     */
    private $ext;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incevio:convert-image-to-webp 
                            {--d|delete : Whether the not found images should be deleted from the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Deletable images ids
     *
     * @var array
     */
    protected $deleteImgs = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->ext = 'webp';
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $delete = $this->option('delete');

        // When the option is not given, ask the user
        if (!$delete) {
            $delete = $this->confirm('Do you want deleted not found images from the database?');
        }

        DB::table('images')->orderBy('id')->chunkById(100, function (Collection $images) {
            foreach ($images as $image) {

                $full_path = Storage::path($image->path);
                if (file_exists($full_path)) {
                    if ($image->extension != $this->ext) {
                        $new_path = image_storage_path() . uniqid() . '.' . $this->ext;

                        $this->comment(PHP_EOL . "Converting '" . $image->path . " -> " . $new_path . "'");

                        $manager = new ImageManager(config('image.driver'));
                        $convertedImg = $manager->read($full_path)->toWebp()->toFilePointer();

                        Storage::put($new_path, $convertedImg);

                        // Update image model
                        DB::table('images')->where('id', $image->id)
                            ->update([
                                'path' => $new_path,
                                'extension' => $this->ext,
                                'size' => Storage::size($new_path)
                            ]);

                        // Delete old image
                        // Storage::delete($full_path);
                        unlink($full_path);
                    }
                } else {
                    $this->deleteImgs[] = $image->id;

                    $this->comment(PHP_EOL . "Convertion of '" . $image->path . "' failed. File not exist.");
                }
            }
        });

        // Delete not found images from database to avoid broken links
        if ($delete && !empty($this->deleteImgs)) {
            DB::table('images')->whereIn('id', $this->deleteImgs)->delete();

            $this->comment(PHP_EOL . "All not found images are deleted from the database.");
        }

        $this->comment(PHP_EOL . "Convertion finished.");

        Artisan::call('optimize:clear');

        return 0;
    }
}
