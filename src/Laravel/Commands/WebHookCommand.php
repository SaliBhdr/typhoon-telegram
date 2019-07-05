<?php
/**
 * User: Salar Bahador
 * Date: 4/26/2019
 * Time: 11:15 PM
 */

namespace SaliBhdr\TyphoonTelegram\Laravel\Commands;

use Illuminate\Console\Command;
use SaliBhdr\TyphoonTelegram\Telegram\Api;

class WebHookCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'telegram:set-webhooks';

    /**    * The console command description.
     *
     * @var string
     */
    protected $description = 'set webhooks for your telegram bot';


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
     *
     * @return void
     *
     * @throws \Exception
     */
    public function handle()
    {

        if ($this->isAnyBotNotDefined()) {

            $this->warn("No bot is defined in config file");

            return;
        }

        $this->emptyLine();
        $this->line("<info>Setting webhooks:</info>");
        $this->emptyLine();

        $row = 1;

        foreach ($this->bots as $this->botName => $this->botSetting) {

            if ($this->botIsActive()) {

                $this->tryInitWebHook($row);

            } else {

                $this->warn(" $row) `{$this->botName}` is deactive");

                $this->incrementDeactive();
            }

            $this->emptyLine();
            $row++;
        }

        $this->showCountLine();
    }

    private function showCountLine()
    {
        $this->line(" <info>{$this->activeBotsCount} active</info> and <warning>{$this->deactiveBotsCount} deactive</warning> webhooks");
    }

    /**
     *
     * @return string
     */
    private function getUrl()
    {
        return rtrim($this->botSetting['baseUrl'] ?? url(), "/");
    }

    /**
     * @param $setting
     *
     * @return bool
     */
    private function botIsActive()
    {
        return $this->botSetting['is_active'] ?? false;
    }

    /**
     * @param $url
     * @param $token
     *
     * @return array|mixed
     * @throws \SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException
     */
    private function setWebHook()
    {
        $webHookResponse = Api::init($this->botSetting['botToken'])
                              ->setWebhook(['url' => "{$this->getUrl()}/{$this->botSetting['botToken']}/webhook"]);

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
     * @param $botName
     * @param $msg
     */
    private function webhookError($row, $msg)
    {
        $this->line(" $row) `{$this->botName}` webhook response: <error>" . $msg . "</error>");

    }

    /**
     * @param $row
     * @param $botName
     */
    private function webhookSuccess($row)
    {
        $this->line(" $row) `{$this->botName}` webhook response: <info>Webhook set</info>");
    }

    /**
     * @param $row
     * @param $botName
     * @param $setting
     *
     * @throws \SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException
     */
    private function initWebHook($row)
    {

        $webHookResponse = $this->setWebHook();

        if (isset($webHookResponse[0]) && $webHookResponse[0] === true) {
            $this->webhookSuccess($row);

            $this->incrementActive();
        } else {
            $this->webhookError($row, 'Webhook Not Set');
            $this->incrementDeactive();
        }
    }

    private function tryInitWebHook($row)
    {
        try {

            $this->initWebHook($row);
        }
        catch (\Exception $e) {

            $this->webhookError($row, $e->getMessage());

            $this->incrementDeactive();
        }
    }
}