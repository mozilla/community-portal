jQuery(function() {
  function getFilter(option) {
    console.log(option.dataset);
    const filter = option.data("filter");
    console.log(filter);
    return filter;
  }

  const $filters = jQuery(".events__filter__option");
  $filters.each((i, filter) => {
    jQuery(filter).on("change", function(e) {
      console.log(filter);
      const filterTitle = getFilter(filter);
      console.log(filterTitle);
      const url = new URL(location.href);
      const params = new URLSearchParams(url.search.slice(1));
      console.log(params.has("tag"));
      if (params.has("tag")) {
        url.searchParams.set("tag", "Localization");
        window.location.href = url;
      }
    });
  });
});
