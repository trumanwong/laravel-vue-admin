<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

if (!function_exists('responseSuccess')) {
    /**
     * Success response
     * @param array|LengthAwarePaginator $data
     * @param string $msg
     * @param array $other
     * @return JsonResponse
     */
    function responseSuccess($data = [], $msg = '操作成功', $other = []): JsonResponse
    {
        $res = [
            'message' => $msg,
            'data' => $data,
            'code' => 200,
        ];

        $res = !empty($other) ? array_merge($res, $other) : $res;
        if ($data instanceof LengthAwarePaginator) {
            $data = $data->toArray();
            $page = [
                'current_page' => (int)$data['current_page'],
                'last_page' => (int)$data['last_page'],
                'per_page' => (int)$data['per_page'],
                'total' => (int)$data['total'],
            ];

            $res['data'] = $data['data'];
            $res['count'] = (int)$data['total'];
            $res['pages'] = $page;
        }

        return response()->json($res);
    }
}

if (!function_exists('responseFailed')) {
    /**
     * Error response
     * @param string $msg
     * @param integer $statusCode
     * @param array $data
     * @return JsonResponse
     */
    function responseFailed(string $msg = '操作失败', int $statusCode = 400, $data = []): JsonResponse
    {
        if (config('app.debug')) {
            return response()->json([
                'message' => $msg,
                'data' => $data,
            ])->setStatusCode($statusCode);
        }

        return response()->json([
            'message' => $msg,
            'data' => $data,
        ])->setStatusCode($statusCode);
    }
}

if (!function_exists('randomString')) {
    /**
     * Return random string with $length
     *
     * @param int $length
     * @return string
     */
    function randomString(int $length = 0): string
    {
        if ($length === 0) {
            $length = mt_rand(10, 100);
        }

        $characters = ' 0123456789 abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($index = 0; $index < $length; $index++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

if (!function_exists('randomDateTime')) {
    /**
     * @return \DateTime
     */
    function randomDateTime(): \DateTime
    {
        $dateTime = new \DateTime();
        $randomHours = mt_rand(0, 1000);
        $dateTime->modify(sprintf('-%s hours', $randomHours));

        return $dateTime;
    }
}

if (!function_exists('randomInArray')) {
    /**
     * @param array $array
     * @return mixed
     */
    function randomInArray($array)
    {
        return $array[array_rand($array)];
    }
}

if (!function_exists('randomBoolean')) {
    /**
     * @return bool
     */
    function randomBoolean(): bool
    {
        return (bool)mt_rand(0, 1);
    }
}


if (!function_exists('vite_assets')) {
    /**
     * @return HtmlString
     */
    function vite_assets(): HtmlString
    {
        $manifest = json_decode(file_get_contents(
            public_path('build/manifest.json')
        ), true);

        return new HtmlString(<<<HTML
        <script type="module" src="/build/{$manifest['resources/js/app.js']['file']}"></script>
        <link rel="stylesheet" href="/build/{$manifest['resources/js/app.js']['css'][0]}">
    HTML);
    }
}