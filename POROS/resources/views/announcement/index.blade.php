<h2>Daftar Pengumuman</h2>

@foreach($announcements as $item)
    <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
        <h3>{{ $item->title }}</h3>
        <p>{{ $item->content }}</p>
        <small>{{ $item->published_at }}</small>
    </div>
@endforeach