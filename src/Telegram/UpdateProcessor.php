<?php


namespace SaliBhdr\TyphoonTelegram\Telegram;


use SaliBhdr\TyphoonTelegram\Laravel\Facades\Telegram;
use SaliBhdr\TyphoonTelegram\Telegram\Commands\CommandBus;
use SaliBhdr\TyphoonTelegram\Telegram\Exceptions\TelegramException;
use SaliBhdr\TyphoonTelegram\Telegram\Response\Models\Update;

class UpdateProcessor
{
    /**
     * @return Update
     * @throws TelegramException
     */
    public function processViaWebhook()
    {
        $update = Telegram::getWebhookUpdates();

        $this->processUpdate($update);

        return $update;
    }

    /**
     * @param bool $all
     *
     * @return Update|Update[]
     * @throws TelegramException
     */
    public function processWithoutWebhook($all = false)
    {
        $updates = Telegram::getUpdates();
        $highestId = -1;

        /** @var Update $update */
        foreach ($updates as $update) {
            $highestId = $update->getUpdateId();
            $this->processUpdate($update);
        }

        //An update is considered confirmed as soon as getUpdates is called with an offset higher than its update_id.
        if ($highestId != -1) {
            $params = [];
            $params['offset'] = $highestId + 1;
            $params['limit'] = 1;
            $updates = Telegram::getUpdates($params);
        }

        if ($all)
            return $updates;

        return current($updates);
    }

    /**
     * Check update object for a command and process. or drives the message into
     * the registered handlers
     *
     * @param Update $update
     *
     * @throws TelegramException
     */
    public function processUpdate(Update $update)
    {
        $handlers = config('telegram.handlers');

        if (!empty($handlers['boot'])) {
            $this->callHandler($handlers['boot'], $update);
        }

        /** @var CommandBus $commandBus */
        $commandBus = Telegram::getCommandBus();

        if ($update->has('message') && $update->getMessage()->isCommand()) {

            $message = $update->getMessage();

            if ($message != null && $message->has('text')) {
                $commandBus->handler($message->getText(), $update, true);
            }
        }

        if (!$commandBus->isCommandHandled()) {

            $messageHandler = null;

            if (!empty($handlers)) {
                switch (true) {
                    case $update->isMessage():
                        $messageHandler = $handlers['message'] ?? null;
                        break;
                    case $update->isCallbackQuery():
                        if ($update->isInlineCallbackQuery())
                            $messageHandler = $handlers['inline_callback'] ?? null;
                        else
                            $messageHandler = $handlers['callback_query'] ?? null;
                        break;
                    case $update->isInlineQuery():
                        $messageHandler = $handlers['inline_query'] ?? null;
                        break;
                }

                $this->callHandler($messageHandler, $update);
            }
        }
    }

    /**
     * calls handlers handle method
     *
     * @param $handler
     * @param $update
     *
     * @throws TelegramException
     */
    private function callHandler($handler, $update)
    {
        if (!empty($handler)) {
            if (is_string($handler)) {
                if (class_exists($handler))
                    $handler = new $handler($update);
                else
                    throw new TelegramException("The `{$handler}` class not exists");
            }

            if ($handler instanceof BaseTelegramFlowHandler)
                $handler->handle();
            else
                throw new TelegramException("The `{$handler}` class must be instance of `" . BaseTelegramFlowHandler::class . "` class");
        }
    }
}
