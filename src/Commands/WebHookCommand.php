<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 11:15 PM
 */

namespace Salibhdr\TyphoonTelegram\Commands;


use Illuminate\Console\Command;
use Salibhdr\TyphoonTelegram\Facades\Telegram;

class WebHookCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tytelegram:set-webhooks';

    /**    * The console command description.
     *
     * @var string
     */
    protected $description = 'set webhooks for your telegram bot';

    /**    * Execute the console command.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function handle()
    {
        $this->line("");

        $bots = config('telegram.bots');

        if (is_null($bots) || !is_array($bots) || empty($bots)) {
            $this->error("No bot is defined in config file");

            return;
        }

        $this->line("<info>Setting webhooks:</info>");

        $row = 1;
        $activeCount = 0;
        $deactiveCount = 0;

        foreach ($bots as $botName => $setting) {

            if($setting['is_active']){
                $activeCount++;

                $baseUrl = $setting['baseUrl'] ?? url();

                $webHookResponse = Telegram::setWebhook(['url' => "{$baseUrl}/{$setting['botToken']}/webhook"], false);

                $this->line("");

                $webHookResponse = $webHookResponse->getRawResponse();

                if (isset($webHookResponse[0]) && $webHookResponse[0] === true)
                    $this->line(" $row) `{$botName}` webhook response: <info>Webhook set</info>");
                else
                    $this->line(" $row) `{$botName}` webhook response: <error>Webhook Not Set</error>");

            }else{
                $deactiveCount++;

                $this->warn(" $row) `{$botName}` is deactive");
            }

            $this->line("");
            $row++;
        }
        $this->line(" <info>{$activeCount} active</info> and <error>{$deactiveCount} deactive</error> webhooks");
    }
}