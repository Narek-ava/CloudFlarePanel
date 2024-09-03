<?php

namespace App\Http\Controllers;

use App\Models\CloudflareAccount;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class CloudflareController extends Controller
{
    protected Client $client;
    protected string $apiBaseUri = 'https://api.cloudflare.com/client/v4/';

    public function index(): JsonResponse
    {
        $accounts = CloudflareAccount::query()->where('user_id', auth()->id())->get();
        return response()->json(['accounts' => $accounts]);
    }

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->apiBaseUri,
        ]);
    }

    /**
     * Update SSL/TLS mode for a given zone.
     *
     * @param Request $request
     * @param string $zoneId
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function updateSSLMode(Request $request, $zoneId): JsonResponse
    {
        $sslMode = $request->input('ssl_mode');
        $account_id = $request->input('account_id');
        $account = CloudflareAccount::query()->where('id', $account_id)->first();

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $response = $this->client->patch("zones/{$zoneId}/settings/ssl", [
            'headers' => [
                'X-Auth-Email' => $account->email,
                'X-Auth-Key' => $account->api_key,
                'Content-Type' => 'application/json',
            ],
            'json' => ['value' => $sslMode],
        ]);

        $responseBody = json_decode($response->getBody(), true);

        if ($responseBody['success']) {
            return response()->json(['message' => 'SSL/TLS mode updated successfully']);
        } else {
            return response()->json(['message' => 'Failed to update SSL/TLS mode'], 500);
        }
    }


    /**
     * Create a new Page Rule for a given zone.
     *
     * @param Request $request
     * @param string $zoneId
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function createPageRule(Request $request, $zoneId): JsonResponse
    {
        $targetUrl = $request->input('url_pattern');
        $actions = $request->input('actions'); // Массив действий
        $account_id = $request->input('account_id');
        $account = CloudflareAccount::query()->where('id', $account_id)->first();
        if (empty($actions)) {
            $actions = [
                [
                    'id' => 'browser_check',
                    'value' => 'on'
                ]
            ];
        }
        try {
            $response = $this->client->post("zones/{$zoneId}/pagerules", [
                'headers' => [
                    'X-Auth-Email' => $account->email,
                    'X-Auth-Key' => $account->api_key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'targets' => [
                        [
                            'target' => 'url',
                            'constraint' => ['operator' => 'matches', 'value' => $targetUrl],
                        ],
                    ],
                    'actions' => $actions,
                    'priority' => 1,
                    'status' => 'active',
                ],
            ]);
            return response()->json(['message' => 'Page rule created successfully']);

        } catch (\Exception $exception) {
            Log::info('CreateRuleError', [$exception->getMessage()]);
            return response()->json(['message' => $exception->getMessage()]);

        }

    }

    /**
     * Delete a Page Rule for a given zone.
     *
     * @param Request $request
     * @param string $zoneId
     * @param string $ruleId
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function deletePageRule(Request $request, $zoneId, $ruleId): JsonResponse
    {
        $account_id = $request->input('account_id');
        $account = CloudflareAccount::query()->where('id', $account_id)->first();
        $response = $this->client->delete("zones/{$zoneId}/pagerules/{$ruleId}",
            [
                'headers' => [
                    'X-Auth-Email' => $account->email,
                    'X-Auth-Key' => $account->api_key,
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $responseBody = json_decode($response->getBody(), true);

        if ($responseBody['success']) {
            return response()->json(['message' => 'Page rule deleted successfully']);
        } else {
            return response()->json(['message' => 'Failed to delete page rule'], 500);
        }
    }

    public function getZoneId(Request $request): JsonResponse
    {
        $domainName = $request->input('name');
        $response = $this->client->get('zones', [
            'query' => [
                'name' => $domainName,
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);
        $zoneId = $responseBody['result'][0]['id'];

        return response()->json(['zoneId' => $zoneId]);
    }

    /**
     * @param CloudflareAccount $account
     * @return Response
     */
    public function showAccount(CloudflareAccount $account): Response
    {

        return Inertia::render('CloudflareAccountSingle', [
            'account' => $account,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addAccount(Request $request): JsonResponse
    {
        $account = CloudflareAccount::query()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'api_key' => $request->input('api_key'),
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Account added successfully', 'account' => $account]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function getDomains(Request $request): JsonResponse
    {
        $response = $this->client->get('zones', ['headers' => [
            'X-Auth-Email' => $request->input('email'),
            'X-Auth-Key' => $request->input('api_key'),
            'Content-Type' => 'application/json',
        ],]);

        $responseBody = json_decode($response->getBody(), true);

        return response()->json($responseBody);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function addDomain(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'api_key' => 'required|string',
            'type' => 'required|string',
        ]);

        $email = $request->input('email');
        $apiKey = $request->input('api_key');
        $domainName = $request->input('domain_name');
        $type = $request->input('type');

        try {
            $response = $this->client->post('zones', [
                'headers' => [
                    'X-Auth-Email' => $email,
                    'X-Auth-Key' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'name' => $domainName,
                    'type' => $type,
                    'jump_start' => false,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['success']) && $result['success']) {
                return response()->json(['message' => 'Domain added successfully', 'result' => $result], 200);
            } else {
                return response()->json(['error' => $result['errors'] ?? 'Unknown error'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function deleteDomain(Request $request): JsonResponse
    {
        $request->validate([
            'api_key' => 'required|string',
            'email' => 'required|email',
            'id' => 'required|string',
        ]);

        $apiKey = $request->input('api_key');
        $email = $request->input('email');
        $zoneId = $request->input('id');

        try {
            $response = $this->client->delete("zones/{$zoneId}", [
                'headers' => [
                    'X-Auth-Email' => $email,
                    'X-Auth-Key' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['success']) && $result['success']) {
                return response()->json(['message' => 'Domain deleted successfully'], 200);
            } else {
                return response()->json(['error' => $result['errors'] ?? 'Unknown error'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @param Request $request
     * @param $zoneId
     * @return array|mixed
     * @throws GuzzleException
     */
    public function getPageRules(Request $request, $zoneId): mixed
    {
        $account_id = $request->input('account_id');
        $account = CloudflareAccount::query()->where('id', $account_id)->first();
        try {
            $response = $this->client->get("zones/{$zoneId}/pagerules", [
                'headers' => [
                    'X-Auth-Email' => $account->email,
                    'X-Auth-Key' => $account->api_key,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if ($responseBody['success']) {
                return $responseBody['result'];
            } else {
                return ['error' => $responseBody['errors']];
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return ['error' => $e->getMessage()];
        }
    }

}
