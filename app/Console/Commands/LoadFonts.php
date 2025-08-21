<?php

// ไฟล์ app/Console/Commands/LoadFonts.php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class LoadFonts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:load-fonts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Thai fonts for PDF generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Loading Thai fonts for PDF generation...');
        
        $fontDir = storage_path('fonts');
        
        if (!file_exists($fontDir)) {
            mkdir($fontDir, 0755, true);
            $this->info('Created fonts directory: ' . $fontDir);
        }
        
        // Check if fonts exist
        $fonts = [
            'THSarabun.ttf',
            'THSarabun Bold.ttf', 
            'THSarabun Italic.ttf',
            'THSarabun BoldItalic.ttf'
        ];
        
        $missingFonts = [];
        $existingFonts = [];
        
        foreach ($fonts as $font) {
            if (!file_exists($fontDir . '/' . $font)) {
                $missingFonts[] = $font;
            } else {
                $existingFonts[] = $font;
            }
        }
        
        if (!empty($existingFonts)) {
            $this->info('Found existing fonts:');
            foreach ($existingFonts as $font) {
                $this->line('✓ ' . $font);
            }
        }
        
        if (!empty($missingFonts)) {
            $this->warn('Missing font files:');
            foreach ($missingFonts as $font) {
                $this->line('✗ ' . $font);
            }
            $this->info('');
            $this->info('Please download THSarabun fonts and place them in: ' . $fontDir);
            $this->info('You can download Thai fonts from:');
            $this->info('- https://fonts.google.com/specimen/Sarabun');
            $this->info('- Or use system fonts like: Tahoma, Arial Unicode MS');
            
            return 1;
        }
        
        $this->info('All fonts are available!');
        $this->info('Fonts directory: ' . $fontDir);
        
        // Test font loading
        $this->info('Testing font permissions...');
        $testFile = $fontDir . '/test.txt';
        
        if (file_put_contents($testFile, 'test') !== false) {
            unlink($testFile);
            $this->info('✓ Fonts directory is writable');
        } else {
            $this->error('✗ Fonts directory is not writable');
            $this->info('Please set permissions: chmod -R 775 ' . $fontDir);
            return 1;
        }
        
        $this->info('✓ Fonts loaded successfully!');
        return 0;
    }
}