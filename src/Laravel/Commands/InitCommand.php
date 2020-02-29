<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 11:15 PM
 */

namespace SaliBhdr\TyphoonTelegram\Laravel\Commands;

use Illuminate\Console\Command;

class InitCommand extends Command
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'telegram:init';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Initial Command for making needed flow handlers to start your project';

    /**
     * Execute the console command.
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->copy('Commands', 'HelpCommand', "Telegram/Commands");
        $this->copy('Commands', 'StartCommand', "Telegram/Commands");
        $this->copy('Handlers', 'BootHandler', "Telegram/Handlers");
        $this->copy('Handlers', 'CallBackFlowHandler', "Telegram/Handlers");
        $this->copy('Handlers', 'InlineCallBackFlowHandler', "Telegram/Handlers");
        $this->copy('Handlers', 'InlineQueryFlowHandler', "Telegram/Handlers");
        $this->copy('Handlers', 'MessageFlowHandler', "Telegram/Handlers");
        $this->copy('Controllers', 'MainBotController', "Http/Controllers/Telegram");

    }

    /**
     * copy stubs
     * @param $folder
     * @param $name
     * @param $destination
     */
    private function copy($folder, $name, $destination)
    {
        $destination = str_replace('/', DIRECTORY_SEPARATOR, $destination);
        $destination = str_replace('\\', DIRECTORY_SEPARATOR, $destination);

        $stubFolder = __DIR__ . DIRECTORY_SEPARATOR . "stubs" . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;

        $stubPath = $stubFolder . $name . '.stub';

        $destinationFolder = app_path($destination) . DIRECTORY_SEPARATOR;

        $copyPath = $destinationFolder . $name . '.php';

        if (!file_exists($copyPath) && file_exists($stubPath)) {
            if (!is_dir($destinationFolder))
                mkdir($destinationFolder, 755);

            if (is_dir($destinationFolder)) {

                file_put_contents($copyPath, file_get_contents($stubPath));

                $this->info($name . ' created successfully.');
            }
            else {
                $this->error('Can not make directory ' . $destinationFolder . ' ');
                $this->line('');
                $this->info("Please check directory creation Privileges or make that directory manually");
            }

        }
    }

}
