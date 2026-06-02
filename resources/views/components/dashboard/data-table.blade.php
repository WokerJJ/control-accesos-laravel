<div class="card shadow-sm">

    <div class="table-responsive">

        <table class="table table-hover mb-0">

            @isset($head)
            <thead>
            {{ $head }}
            </thead>
            @endisset

            <tbody>
            {{ $slot }}
            </tbody>

        </table>

    </div>

</div>
