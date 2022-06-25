<div class="h-full p-0 card">
    <header class="flex items-center justify-between p-2 border-b">
        <h2 class="flex items-center">
            <div class="w-6 h-6 mr-1 text-grey-80">
                @cp_svg('pages')
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
            @switch($config['display'] ?? 'table')
                @case('bar-chart')
                @case('bar')
                <bar-chart :chartdata="mvpChartData"/>
                @break

                @case('line-chart')
                @case('line')
                <line-chart :chartdata="mvpChartData"/>
                @break

                @case('pie-chart')
                @case('pie')
                <pie-chart :chartdata="mvpChartData"/>
                @break

                @case('doughnut-chart')
                @case('doughnut')
                <doughnut-chart :chartdata="mvpChartData"/>
                @break

                @case('radar-chart')
                @case('radar')
                <radar-chart :chartdata="mvpChartData"/>
                @break

                @case('polar-chart')
                @case('polar')
                <polar-chart :chartdata="mvpChartData"/>
                @break

                @case('bubble-chart')
                @case('bubble')
                <bubble-chart :chartdata="mvpChartData"/>
                @break

                @case('scatter-chart')
                @case('scatter')
                <scatter-chart :chartdata="mvpChartData"/>
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
@if ($data)
    <script>
        const mvpChartData = {
            labels: {!! json_encode($data->pluck("pageTitle")) !!},
            datasets: [
                {
                    label: "Views",
                    data: {!! json_encode($data->pluck("pageViews"))!!},
                }]
        };
    </script>
@endif
