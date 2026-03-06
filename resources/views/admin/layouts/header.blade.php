<div style="background:black;color:white;padding:10px;">
    Xin chào: {{ auth()->user()->name ?? '' }}

    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit">Logout</button>
    </form>
</div>
