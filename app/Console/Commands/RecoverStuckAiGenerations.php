<?php

namespace App\Console\Commands;

use App\Models\AiArticleGeneration;
use Illuminate\Console\Command;

class RecoverStuckAiGenerations extends Command
{
    /**
     * @var string
     */
    protected $signature = 'ai:recover-stuck {--minutes=30 : Umur minimum (menit) baris processing yang dianggap nyangkut}';

    /**
     * @var string
     */
    protected $description = 'Tandai generasi AI yang nyangkut di status processing sebagai failed';

    public function handle(): int
    {
        $minutes = max(1, (int) $this->option('minutes'));
        $cutoff = now()->subMinutes($minutes);

        $affected = AiArticleGeneration::where('status', 'processing')
            ->where('updated_at', '<', $cutoff)
            ->update([
                'status' => 'failed',
                'error_message' => "Dipulihkan otomatis: status 'processing' lebih dari {$minutes} menit (proses kemungkinan terputus).",
                'updated_at' => now(),
            ]);

        $this->info("Generasi nyangkut yang ditandai failed: {$affected}");

        return self::SUCCESS;
    }
}
