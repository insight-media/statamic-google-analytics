<div class="h-full p-0 card">
    <header class="flex items-center justify-between p-2 border-b">
        <h2 class="flex items-center">
            <div class="w-6 h-6 mr-1 text-grey-80">
                @cp_svg('file-content-list')
            </div>
            <span>{{ __('statamic-google-analytics::messages.most-viewed-pages-header', ['days' => $config['days'] ?? 30]) }}</span>
        </h2>
    </header>
    <div class="p-2">
        @if ($message)
            <p class="bg-red-lighter text-red-dark p-2 border-red-dark rounded">
                {{$message}}
            </p>
        @endif
        @if ($data)
            @php
                $chartData = [
                    'labels' => $data->pluck('pageTitle'),
                    'datasets' => [['label' => __('statamic-google-analytics::messages.views'), 'data' => $data->pluck('pageViews')]],
                ];
            @endphp
            @switch($config['display'] ?? 'table')
                @case('bar-chart')
                @case('bar')
                <bar-chart :chartdata="{{ Illuminate\Support\Js::from($chartData) }}"/>
                @break

                @case('line-chart')
                @case('line')
                <line-chart :chartdata="{{ Illuminate\Support\Js::from($chartData) }}"/>
                @break

                @case('pie-chart')
                @case('pie')
                <pie-chart :chartdata="{{ Illuminate\Support\Js::from($chartData) }}"/>
                @break

                @case('doughnut-chart')
                @case('doughnut')
                <doughnut-chart :chartdata="{{ Illuminate\Support\Js::from($chartData) }}"/>
                @break

                @case('radar-chart')
                @case('radar')
                <radar-chart :chartdata="{{ Illuminate\Support\Js::from($chartData) }}"/>
                @break

                @case('polar-chart')
                @case('polar')
                <polar-chart :chartdata="{{ Illuminate\Support\Js::from($chartData) }}"/>
                @break

                @case('bubble-chart')
                @case('bubble')
                <bubble-chart :chartdata="{{ Illuminate\Support\Js::from($chartData) }}"/>
                @break

                @case('scatter-chart')
                @case('scatter')
                <scatter-chart :chartdata="{{ Illuminate\Support\Js::from($chartData) }}"/>
                @break

                @default
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>{{ __('statamic-google-analytics::messages.page') }}</th>
                        <th class="text-right">{{ __('statamic-google-analytics::messages.views') }}</th>
                    </tr>
                    </thead>
                    <!---->
                    <tbody tabindex="0">
                    @foreach ($data as $item)
                        <tr>
                            <td><a target="_blank" href="{{  url($item['url']) }}">{{ $item['pageTitle']}}</a></td>
                            <td class="text-right"> {{ (NumberFormatter::create(app()->getLocale(), NumberFormatter::DECIMAL))->format($item['pageViews']) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endswitch
        @endif
    </div>

</div>
