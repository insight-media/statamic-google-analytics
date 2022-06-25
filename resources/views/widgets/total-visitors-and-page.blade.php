<div class="h-full p-0 card">
    <header class="flex items-center justify-between p-2 border-b">
        <h2 class="flex items-center">
            <div class="w-6 h-6 mr-1 text-grey-80">
                @cp_svg('pages')
            </div>
            <span>{{ __('statamic-google-analytics::messages.total-visitors-and-page-header', ['days' => $config['days'] ?? 30]) }}</span>
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
                <bar-chart :chartdata="tvpChartData"/>
                @break

                @case('line-chart')
                @case('line')
                <line-chart :chartdata="tvpChartData"/>
                @break

                @case('pie-chart')
                @case('pie')
                <pie-chart :chartdata="tvpChartData"/>
                @break

                @case('doughnut-chart')
                @case('doughnut')
                <doughnut-chart :chartdata="tvpChartData"/>
                @break

                @case('radar-chart')
                @case('radar')
                <radar-chart :chartdata="tvpChartData"/>
                @break

                @case('polar-chart')
                @case('polar')
                <polar-chart :chartdata="tvpChartData"/>
                @break

                @case('bubble-chart')
                @case('bubble')
                <bubble-chart :chartdata="tvpChartData"/>
                @break

                @case('scatter-chart')
                @case('scatter')
                <scatter-chart :chartdata="tvpChartData"/>
                @break

                @default
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>{{ __('statamic-google-analytics::messages.date') }}</th>
                        <th>{{ __('statamic-google-analytics::messages.visitors') }}</th>
                        <th class="text-right">{{ __('statamic-google-analytics::messages.views') }}</th>
                    </tr>
                    </thead>
                    <!---->
                    <tbody tabindex="0">
                    @foreach ($data as $item)
                        <tr>
                            <td> {{ $item['date'] }}</td>
                            <td> {{ $item['visitors']}} </td>
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
        const tvpChartData = {
            labels: {!! json_encode($data->map(function($row) {
            $row['date'] = $row['date']->format("Y-m-d");
            return $row;
         })->pluck("date")) !!},
            datasets: [{
                label: "{{ __('statamic-google-analytics::messages.visitors') }}",
                data: {!! json_encode($data->pluck("visitors"))!!},
            },
                {
                    label: "{{ __('statamic-google-analytics::messages.views') }}",
                    data: {!! json_encode($data->pluck("pageViews"))!!},
                }]
        };

    </script>
@endif
