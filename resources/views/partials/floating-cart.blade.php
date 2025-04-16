<div id="floating-cart" class="position-fixed bottom-4 end-4" style="z-index:1050;">
    <a href="{{ route('shop', $tenant) }}" class="btn btn-main shadow-lg rounded-pill rounded-circle position-relative">
        <i class="fas fa-shopping-cart"></i>
        <span id="floating-cart-count"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            0
        </span>
    </a>
</div>
