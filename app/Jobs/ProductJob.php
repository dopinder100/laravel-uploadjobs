<?php

namespace App\Jobs;

use App\Models\ImportProduct;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class ProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $path;
    /**
     * Create a new job instance.
     */
    public function __construct($path)
    {
        $this->path = $path;
    }


    /**
     * Execute the job.
     */
    public function handle()
    {

        $job = ImportProduct::where('job_id', $this->job->getJobId())->first();
        if($job != null)
        {
            $job->status = "processing";
            $job->save();
        }

        try
        {

            $csvFile = fopen(storage_path().'/app/'.$this->path, 'r');

            if ($csvFile) {
                $headers = fgetcsv($csvFile); // Read the first row as headers.

                if (mb_detect_encoding($headers[0]) === 'UTF-8') {
                    // delete possible BOM
                    // not all UTF-8 files start with these three bytes
                    $headers[0] = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $headers[0]);
                }

                $headers_data=[];
                foreach($headers as $head)
                {
                    $head = str_replace("\n", "", $head);
                    array_push($headers_data, trim($head));
                }


                $desiredColumns = ['UNIQUE_KEY', 'PRODUCT_TITLE', 'PRODUCT_DESCRIPTION', 'STYLE#', 'SANMAR_MAINFRAME_COLOR', 'SIZE', 'COLOR_NAME', 'PIECE_PRICE'];

                $data = [];

                while (($row = fgetcsv($csvFile)) !== false) {
                    $row = array_combine($headers_data, $row);

                    // Extract only the desired columns.
                    $extractedData = [];
                    foreach ($desiredColumns as $column) {
                        if (isset($row[$column])) {
                            $extractedData[$column] = $row[$column];
                        } else {
                            $extractedData[$column] = null;
                        }
                    }

                    $data[] = $extractedData;
                }

                fclose($csvFile);

                foreach($data as $d)
                {
                    Product::updateOrCreate(["UNIQUE_KEY"=>$d['UNIQUE_KEY']],$d);
                }

                Log::info($data);
            }
        }
        catch(\Exception $e)
        {
            $job = ImportProduct::where('job_id', $this->job->getJobId())->first();

            if($job != null)
            {
                $job->status = "failed";
                $job->save();
            }
        }


        // status completed
        $job = ImportProduct::where('job_id', $this->job->getJobId())->first();

        if($job != null)
        {
            $job->status = "completed";
            $job->save();
        }
    }

    public function failed(\Exception $e)
    {
        $job = ImportProduct::where('job_id', $this->job->getJobId())->first();

        if($job != null)
        {
            $job->status="failed";
            $job->save();
        }
    }


}
