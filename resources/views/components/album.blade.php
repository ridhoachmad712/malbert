@if(!empty($data['album']['media']))
<section class="story-album" aria-label="Album foto dan video" aria-roledescription="carousel" data-album tabindex="0">
<div class="section-heading"><div><span class="eyebrow">KENANGAN BERSAMA</span><h2>Album cerita kita</h2></div><span>{{ count($data['album']['media']) }} media</span></div>
<div class="album-stage">
@foreach($data['album']['media'] as $slide)
<figure class="album-slide" data-album-slide role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration }} dari {{ $loop->count }}" @if(!$loop->first) hidden @endif>
<div class="album-frame" data-album-frame data-src="{{ $slide['embed'] }}" data-title="{{ $slide['caption'] }}" data-kind="{{ $slide['kind'] }}"><a href="{{ $slide['url'] }}" target="_blank" rel="noopener noreferrer">Buka {{ $slide['kind']==='video' ? 'video' : 'foto' }} di Google Drive ↗</a></div>
<figcaption><span>{{ $slide['kind']==='video' ? '▶ Video' : '▧ Foto' }}</span> {{ $slide['caption'] }}</figcaption>
<a class="album-fallback" href="{{ $slide['url'] }}" target="_blank" rel="noopener noreferrer">Media tidak tampil? Buka di Google Drive ↗</a>
</figure>
@endforeach
</div>
<div class="album-controls" hidden data-album-controls>
<button type="button" class="button outline" data-album-prev aria-label="Media sebelumnya">← Sebelumnya</button>
<span data-album-count role="status" aria-live="polite">1 / {{ count($data['album']['media']) }}</span>
<button type="button" class="button" data-album-next aria-label="Media berikutnya">Berikutnya →</button>
</div>
<noscript><p>Aktifkan JavaScript untuk berpindah slide. <a href="{{ $data['album']['url'] }}" target="_blank" rel="noopener noreferrer">Buka seluruh album di Google Drive</a>.</p></noscript>
</section>
@endif
