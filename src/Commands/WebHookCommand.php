<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 11:15 PM
 */

namespace Salibhdr\TyphoonTelegram\Commands;


use Illuminate\Console\Command;
use Salibhdr\TyphoonTelegram\Facades\TyTelegram;

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

        $webhooks = config('telegram.webhooks');

        if (is_null($webhooks) || !is_array($webhooks) || empty($webhooks)) {
            $this->error("No webhook is specified");

            return;
        }

        $this->line("<info>Setting webhooks:</info>");

        $count = 1;
        $activeCount = 0;
        $deactiveCount = 0;

        foreach ($webhooks as $botName => $webhook) {

            if($webhook['is_active']){
                $activeCount++;

                $baseUrl = $webhook['baseUrl'] ?? url();

                $webHookResponse = TyTelegram::setWebhook(['url' => "{$baseUrl}/{$webhook['botToken']}/webhook"], false);

                $this->line("");

                $webHookResponse = $webHookResponse->getRawResponse();

                if (isset($webHookResponse[0]) && $webHookResponse[0] === true)
                    $this->line(" $count) `{$botName}` webhook response: <info>Webhook set</info>");
                else
                    $this->line(" $count) `{$botName}` webhook response: <error>Webhook Not Set</error>");

            }else{
                $deactiveCount++;

                $this->warn(" $count) `{$botName}` is deactive");
            }

            $this->line("");
            $count++;
        }
        $this->line(" <info>{$activeCount} active</info> and <error>{$deactiveCount} deactive</error> webhooks");
    }
}