<?php

namespace App\Modules\Finnotech\OAK\V2;

use App\Modules\Banking\Models\Account;
use Exception;
use App\Modules\Banking\Models\Transfer;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;


class Oak
{

    use OakRepo;

    private string $methodUrl;
    private string $urlParameters;

    private array $headers;


    private const SERVER = '1.1.1.1';
    private const BASE_URL = '/oak/v2/clients/312/';

    public const TRANSFER_TO_URL = 'transferTo';

    private const TRACK_ID = 'trackId';


    /**
     * @throws Exception
     */
    public function transferTo(array $data, Account $account)
    {
        $requestData = array_merge(self::getTransferExpectedData($account), $data);
        $trackId = Transfer::generateTransferTrackId();

        $this->methodUrl = self::TRANSFER_TO_URL;
        $this->setUrlParameters([self::TRACK_ID, $trackId]);
        // $this->setHeaders(['auth', 'code']); Authorization_Code|Client_Credential

        if (Config::get('app.fake_oak')) {
            return $this->fakeTransferToResponse($requestData, $trackId);
        }

        $response = $this->sendRequest($requestData);

        if ($response->status() == 200 && $response['status'] == 'DONE') {
            return $response;
        } else {
            throw new Exception('Something wrong happend with Transfering Data with Oak');
        }

    }


    /**
     * @param mixed $urlParameters
     * Accept [key, value]
     * @throws Exception
     */
    private function setUrlParameters(array $urlParameters): void
    {
        $parameter = $urlParameters[0] . '=' . $urlParameters[1];
        if (!isset($this->urlParameters)) {
            $this->urlParameters = $parameter;
        } else {
            $this->urlParameters .= '&' . $parameter;
        }
    }


    /**
     * @param array $headers
     * Accept [key, value]
     * @throws Exception
     */
    private function setHeaders(array $headers): void
    {
        $this->headers[$headers[0]] = $headers[1];
    }


    private function generateRequestUrl(): string
    {
        $url = Oak::SERVER . Oak::BASE_URL . $this->methodUrl;
        if ($this->urlParameters == null) {
            $url .= '?' . $this->urlParameters;
        }
        return $url;
    }


    private function sendRequest(array $data): PromiseInterface|Response
    {

        $url = $this->generateRequestUrl();
        $headers = array_merge($this->headers, ['Content-Type' => 'application/json']);

        return Http::withHeaders($headers)->post($url, $data);

    }


}
