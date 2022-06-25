<div class="h-full p-0 card">
    <header class="flex items-center justify-between p-2 border-b">
        <h2 class="flex items-center">
            <div class="w-6 h-6 mr-1 text-grey-80">
                @cp_svg('home-page')
            </div>
            <span>{{ __('statamic-google-analytics::messages.top-browsers-header', ['days' => $config['days'] ?? 30]) }}</span>
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
                <bar-chart :chartdata="tbChartData"/>
                @break

                @case('line-chart')
                @case('line')
                <line-chart :chartdata="tbChartData"/>
                @break

                @case('pie-chart')
                @case('pie')
                <pie-chart :chartdata="tbChartData"/>
                @break

                @case('doughnut-chart')
                @case('doughnut')
                <doughnut-chart :chartdata="tbChartData" :options="{legend: {display: true}"/>
                @break

                @case('radar-chart')
                @case('radar')
                <radar-chart :chartdata="tbChartData"/>
                @break

                @case('polar-chart')
                @case('polar')
                <polar-chart :chartdata="tbChartData"/>
                @break

                @case('bubble-chart')
                @case('bubble')
                <bubble-chart :chartdata="tbChartData"/>
                @break

                @case('scatter-chart')
                @case('scatter')
                <scatter-chart :chartdata="tbChartData"/>
                @break

                @default
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>{{ __('statamic-google-analytics::messages.browser') }}</th>
                        <th class="text-right">{{ __('statamic-google-analytics::messages.sessions') }}</th>
                    </tr>
                    </thead>
                    <!---->
                    <tbody tabindex="0">
                    @foreach ($data as $item)
                        <tr>
                            <td> {{$item['browser'] }}</td>
                            <td class="text-right"> {{ (NumberFormatter::create(app()->getLocale(), NumberFormatter::DECIMAL))->format($item['sessions']) }}</td>
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
        const tbChartData = {
            labels: {!! json_encode($data-> pluck("browser")) !!},
            datasets: [{
                label: "{{ __('statamic-google-analytics::messages.top-browsers-header', ['days' => $config['days'] ?? 30 ]) }}",
                data: {!! json_encode($data -> pluck("sessions"))!!},
            }]
        };
    </script>
@endif
