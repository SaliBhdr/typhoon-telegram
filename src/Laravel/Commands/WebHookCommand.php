<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 11:15 PM
 */

namespace SaliBhdr\TyphoonTelegram\Laravel\Commands;

use Illuminate\Console\Command;
use SaliBhdr\TyphoonTelegram\Laravel\Facades\Telegram;
use SaliBhdr\TyphoonTelegram\Telegram\Request\Client;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\Message;
use Symfony\Component\Console\Input\InputOption;

class WebhookCommand extends Command
{

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'telegram:webhook';

    /**    * The console command description.
     * @var string
     */
    protected $description = 'Set webhooks for your telegram bot';


    private $bots;
    private $botName;
    private $botSetting;
    private $activeBotsCount   = 0;
    private $deactiveBotsCount = 0;

    public function __construct()
    {
        $this->setBots();

        parent::__construct();
    }

    /**    * Execute the console command.
     * @return void
     * @throws \Exception
     */
    public function handle()
    {

        if ($this->isAnyBotNotDefined()) {

            $this->warn("No bot is defined in config file");

            return;
        }

        $this->emptyLine();
        $this->printStartMessage();
        $this->emptyLine();

        $row = 1;

        foreach ($this->bots as $this->botName => $this->botSetting) {

            if ($this->botIsActive()) {

                $this->tryInitWebhook($row);
                sleep(rand(1,5));

            }
            else {

                $this->warn(" $row) '{$this->botName}' is not active");

                $this->incrementDeactive();
            }

            $this->emptyLine();
            $row++;
        }

        if (!$this->isPrintModeRequested() && !$this->isInfoRequested())
            $this->showCountLine();
    }

    /**
     * @return bool
     */
    private function isPrintModeRequested()
    {
        return $this->input->getOption('print');
    }

    /**
     * @return bool
     */
    private function isInfoRequested()
    {
        return $this->input->getOption('info');
    }

    private function showCountLine()
    {
        $this->info(" {$this->activeBotsCount} active");
        $this->warn(" {$this->deactiveBotsCount} not active");
    }

    /**
     * @return string
     */
    private function getUrl()
    {
        return rtrim($this->botSetting['baseUrl'] ?? url(), "/");
    }


    /**
     * @return bool
     */
    private function botIsActive()
    {
        return $this->botSetting['is_active'] ?? false;
    }


    /**
     * @return array|mixed
     */
    private function setWebhook()
    {
        /**@var Message $webHookResponse */
        $webHookResponse = Telegram::token($this->botSetting['botToken'])
                              ->setWebhook(['url' => $this->webHookUrl()]);

        return $webHookResponse->getRawResponse();
    }

    /**
     * @return void
     */
    private function incrementActive()
    {
        $this->activeBotsCount++;
    }

    /**
     * @return void
     */
    private function incrementDeactive()
    {
        $this->deactiveBotsCount++;
    }

    /**
     * @return void
     */
    private function emptyLine()
    {
        $this->line("");
    }

    /**
     * @return void
     */
    private function setBots()
    {
        $this->bots = config('telegram.bots');
    }

    /**
     * @return bool
     */
    private function isAnyBotNotDefined()
    {
        return is_null($this->bots) || !is_array($this->bots) || empty($this->bots);
    }

    /**
     * @param $row
     * @param $msg
     */
    private function webhookError($row, $msg)
    {
        $this->line(" $row) '{$this->botName}' webhook error: <error>" . $msg . "</error>");

    }

    /**
     * @param $row
     */
    private function webhookSuccess($row)
    {
        $this->line(" $row) '{$this->botName}' webhook response: <info>Webhook set</info>");
    }


    /**
     * @param $row
     *
     */
    private function initWebhook($row)
    {
        if ($this->isInfoRequested()) {
            $this->info($row . ') `' . $this->botName . '`:' . Client::BASE_BOT_URL . $this->botSetting['botToken'] . '/getWebhookInfo');

        }
        elseif ($this->isPrintModeRequested()) {
            $this->info($row . ') `' . $this->botName . '`:' . Client::BASE_BOT_URL . $this->botSetting['botToken'] . '/setWebhook?url=' . $this->webHookUrl());
        }
        else {
            $webHookResponse = $this->setWebhook();

            if (isset($webHookResponse[0]) && $webHookResponse[0] === true) {
                $this->webhookSuccess($row);

                $this->incrementActive();
            }
            else {
                $this->webhookError($row, 'Webhook Not Set');
                $this->incrementDeactive();
            }
        }

    }

    /**
     * @return string
     */
    private function webHookUrl()
    {
        return "{$this->getUrl()}/{$this->botSetting['botToken']}/webhook";
    }

    /**
     * @param $row
     */
    private function tryInitWebhook($row)
    {
        try {
            $this->initWebhook($row);
        }
        catch (\Exception $e) {

            $this->webhookError($row, $e->getMessage());

            $this->incrementDeactive();
        }
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            new InputOption('--print', null, InputOption::VALUE_NONE, 'Just printing webhooks without setting them'),
            new InputOption('--info', null, InputOption::VALUE_NONE, 'Generates urls to get info about webhook'),
        ];
    }

    private function printStartMessage() : void
    {
        if ($this->isInfoRequested()) {
            $this->info(" Open these links in your browser to get info about your bots webhooks ");
            $this->line(" Webhook info urls are: ");
        }
        elseif ($this->isPrintModeRequested()) {
            $this->info(" Open these links in your browser to set webhooks to your bot ");
            $this->info(" Webhook urls are: ");
        }
        else {
            $this->info(" Setting webhooks: ");
        }
    }
}
