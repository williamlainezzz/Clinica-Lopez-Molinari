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
                color: #e8f1ff;
                background: linear-gradient(180deg, rgba(19, 44, 86, 0.98) 0%, rgba(14, 33, 67, 0.98) 100%);
                border: 1px solid rgba(96, 165, 250, 0.18);
                border-radius: 0.85rem;
                padding: 0.85rem 0.9rem;
                box-shadow: inset 0 1px 0 rgba(147, 197, 253, 0.12);
            }
            .agenda-calendar__day {
                height: 120px;
                border-radius: 0.75rem;
                border: 1px solid rgba(96, 165, 250, 0.16);
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
                color: #0f172a;
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

            html[data-theme='dark'] .agenda-calendar__day {
                background: linear-gradient(180deg, rgba(15, 23, 42, 0.98) 0%, rgba(12, 20, 37, 0.98) 100%);
                border-color: rgba(96, 165, 250, 0.18);
                box-shadow: inset 0 1px 0 rgba(147, 197, 253, 0.05);
            }

            html[data-theme='dark'] .agenda-calendar__day.is-muted {
                background: linear-gradient(180deg, rgba(17, 24, 39, 0.94) 0%, rgba(11, 18, 32, 0.94) 100%);
                color: #64748b;
            }

            html[data-theme='dark'] .agenda-calendar__day-number {
                color: #dbeafe;
            }

            html[data-theme='dark'] .agenda-calendar__day.is-muted .agenda-calendar__day-number {
                color: #7c93b6;
            }

            html[data-theme='dark'] .agenda-calendar__day.is-today {
                border: 2px solid #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18);
            }
        </style>
    @endpush
@endonce
