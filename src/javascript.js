document.addEventListener("DOMContentLoaded", () => {
  const searchForm = document.querySelector("#assortiment-search");

  if (!searchForm) {
    return;
  }

  const grid = document.querySelector(".assortiment-grid");
  const products = Array.from(document.querySelectorAll(".product"));
  const noProductsMessage = document.querySelector(".no-products-message");
  const searchInput = searchForm.elements.zoek;
  const locationSelect = searchForm.elements.standplaats;
  const sortSelect = searchForm.elements.sortering;

  const params = new URLSearchParams(window.location.search);

  const locations = [...new Set(products.map((product) => product.dataset.standplaats).filter(Boolean))].sort();
  locations.forEach((location) => {
    const option = document.createElement("option");
    option.value = location;
    option.textContent = location;
    locationSelect.append(option);
  });

  searchInput.value = params.get("zoek") || "";
  locationSelect.value = params.get("standplaats") || "";
  sortSelect.value = params.get("sortering") || "";

  const updateUrl = () => {
    const nextParams = new URLSearchParams();

    if (searchInput.value.trim() !== "") {
      nextParams.set("zoek", searchInput.value.trim());
    }

    if (locationSelect.value !== "") {
      nextParams.set("standplaats", locationSelect.value);
    }

    if (sortSelect.value !== "") {
      nextParams.set("sortering", sortSelect.value);
    }

    const queryString = nextParams.toString();
    const nextUrl = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;
    window.history.replaceState({}, "", nextUrl);
  };

  const applySearch = () => {
    const searchTerm = searchInput.value.trim().toLowerCase();
    const chosenLocation = locationSelect.value;
    const sortDirection = sortSelect.value;
    let visibleCount = 0;

    const sortedProducts = [...products].sort((firstProduct, secondProduct) => {
      if (sortDirection === "") {
        return products.indexOf(firstProduct) - products.indexOf(secondProduct);
      }

      const firstPrice = Number(firstProduct.dataset.price);
      const secondPrice = Number(secondProduct.dataset.price);

      return sortDirection === "ASC" ? firstPrice - secondPrice : secondPrice - firstPrice;
    });

    sortedProducts.forEach((product) => {
      const productName = product.dataset.name.toLowerCase();
      const matchesSearch = productName.includes(searchTerm);
      const matchesLocation = chosenLocation === "" || product.dataset.standplaats === chosenLocation;
      const isVisible = matchesSearch && matchesLocation;

      product.hidden = !isVisible;
      grid.append(product);

      if (isVisible) {
        visibleCount += 1;
      }
    });

    noProductsMessage.hidden = visibleCount > 0;
    updateUrl();
  };

  searchForm.addEventListener("submit", (event) => {
    event.preventDefault();
    applySearch();
  });

  locationSelect.addEventListener("change", applySearch);
  sortSelect.addEventListener("change", applySearch);
  searchInput.addEventListener("input", applySearch);

  applySearch();
});
