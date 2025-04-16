<div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-4">
    <div class="card text-center border-0 shadow rounded-4 p-3 h-100 cursor-pointer"
        onclick="openCartModal(
            {{ $product->id }},
            {{ json_encode($category->additionals) }},
            '{{ $product->name }}',
            {{ $product->price }},
            '{{ $category->name }}',
            '{{ $product->name }}'
        )"
        style="transition: transform 0.2s;">

        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.jpg') }}"
            class="card-img-top mx-auto rounded-circle object-fit-cover" style="width: 120px; height: 120px;"
            alt="{{ $product->name }}">

        <div class="card-body p-2">
            <h6 class="fw-bold mb-1">{{ $product->name }}</h6>
            <p class="text-muted mb-1 small">{{ $product->description ?? '' }}</p>

            <span class="text-success fw-bold d-block">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
        </div>
    </div>
</div>

<style>
    .card.cursor-pointer:hover {
        transform: scale(1.03);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }
</style>
