@once
    @push('css')
        <style>
            .agenda-calendar {
                border-collapse: separate;
                border-spacing: 0.35rem;
                width: 100%;
            }
            .agenda-calendar th {
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.08em;
                color: #6c757d;
            }
            .agenda-calendar__day {
                height: 120px;
                border-radius: 0.75rem;
                border: 1px solid #e9ecef;
                padding: 0.35rem 0.5rem;
                background: #fff;
                position: relative;
            }
            .agenda-calendar__day.is-muted {
                background: #f8f9fa;
                color: #adb5bd;
            }
            .agenda-calendar__day.is-today {
                border: 2px solid #0d6efd;
                box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
            }
            .agenda-calendar__day-number {
                font-weight: 600;
                font-size: 0.95rem;
            }
            .agenda-calendar__pill {
                display: block;
                width: 100%;
                font-size: 0.7rem;
                padding: 0.25rem 0.35rem;
                border-radius: 0.5rem;
                margin-top: 0.25rem;
                color: #fff;
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;
                cursor: pointer;
            }
            .agenda-calendar__pill.bg-soft {
                color: #0d6efd;
                background: rgba(13,110,253,.12);
            }
            .agenda-calendar__legend span {
                display: inline-flex;
                align-items: center;
                font-size: 0.85rem;
                margin-right: 1rem;
                color: #6c757d;
            }
            .agenda-calendar__legend i {
                font-size: 0.65rem;
                margin-right: 0.35rem;
            }
        </style>
    @endpush
@endonce
