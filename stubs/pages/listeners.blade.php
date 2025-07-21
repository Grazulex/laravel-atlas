<!-- Listeners Page -->
<div id="listeners" class="page">
    <div class="card">
        <div class="card-header">
            <h2>👂 Event Listeners</h2>
        </div>
        <div class="card-body">
            @if(isset($data['listeners']))
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Listener</th>
                            <th>Event</th>
                            <th>Dependencies</th>
                            <th>Jobs Dispatched</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['listeners'] as $listener)
                        <tr>
                            <td>{{ class_basename($listener['class_name']) }}</td>
                            <td>{{ class_basename($listener['event']) }}</td>
                            <td>
                                @if(isset($listener['dependencies']))
                                    {{ implode(', ', array_map('class_basename', $listener['dependencies'])) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(isset($listener['jobs']))
                                    {{ implode(', ', array_map('class_basename', $listener['jobs'])) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No listeners found.</p>
            @endif
        </div>
    </div>
</div>
