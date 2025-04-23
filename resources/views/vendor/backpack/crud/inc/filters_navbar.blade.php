<nav class="navbar navbar-expand-lg navbar-filters mb-0 py-0 shadow-none">
      {{-- Brand and toggle get grouped for better mobile display --}}
      <a class="nav-item d-none d-lg-block my-auto"><span class="la la-filter"></span></a>
      <button class="navbar-toggler ms-3"
              type="button"
              data-toggle="collapse"  {{-- for Bootstrap v4 --}}
              data-target="#bp-filters-navbar" {{-- for Bootstrap v4 --}}
              data-bs-toggle="collapse"   {{-- for Bootstrap v5 --}}
              data-bs-target="#bp-filters-navbar"   {{-- for Bootstrap v5 --}}
              aria-controls="bp-filters-navbar"
              aria-expanded="false"
              aria-label="{{ trans('backpack::crud.toggle_filters') }}">
        <span class="la la-filter"></span> {{ trans('backpack::crud.filters') }}
      </button>

      {{-- Collect the nav links, forms, and other content for toggling --}}
      <div class="collapse navbar-collapse" id="bp-filters-navbar">
        <ul class="nav navbar-nav">
          {{-- THE ACTUAL FILTERS --}}
    			@foreach ($crud->filters() as $filter)
    				@includeFirst($filter->getNamespacedViewWithFallbacks())
    			@endforeach
          <li class="nav-item"><a href="#" id="remove_filters_button" class="nav-link {{ count(Request::input()) != 0 ? '' : 'invisible' }}"><i class="la la-eraser"></i> {{ trans('backpack::crud.remove_filters') }}</a></li>
        </ul>
      </div>{{-- /.navbar-collapse --}}
  </nav>

@push('crud_list_scripts')
    @basset('https://cdn.jsdelivr.net/npm/urijs@1.19.11/src/URI.min.js')
    <script>
      function addOrUpdateUriParameter(uri, parameter, value) {
            var new_url = normalizeAmpersand(uri);

            new_url = URI(new_url).normalizeQuery();

            // this param is only needed in datatables persistent url redirector
            // not when applying filters so we remove it.
            if (new_url.hasQuery('persistent-table')) {
                new_url.removeQuery('persistent-table');
            }

            if (new_url.hasQuery(parameter)) {
              new_url.removeQuery(parameter);
            }

            if (value !== '' && value != null) {
              new_url = new_url.addQuery(parameter, value);
            }

            $('#remove_filters_button').toggleClass('invisible', !new_url.query());

        return new_url.toString();

      }

      function updateDatatablesOnFilterChange(filterName, filterValue, update_url = false, debounce = 500) {
        // behaviour for ajax table
        var current_url = crud.table.ajax.url();
        var new_url = addOrUpdateUriParameter(current_url, filterName, filterValue);

        new_url = normalizeAmpersand(new_url);

        // add filter to URL
        crud.updateUrl(new_url);
        crud.table.ajax.url(new_url);

        // when we are clearing ALL filters, we would not update the table url here, because this is done PER filter
        // and we have a function that will do this update for us after all filters had been cleared.
        if(update_url) {
          // replace the datatables ajax url with new_url and reload it
          callFunctionOnce(function() { refreshDatatablesOnFilterChange(new_url) }, debounce, 'refreshDatatablesOnFilterChange');
        }

        return new_url;
      }

      /**
       * calls the function func once within the within time window.
       * this is a debounce function which actually calls the func as
       * opposed to returning a function that would call func.
       * 
       * @param func    the function to call
       * @param within  the time window in milliseconds, defaults to 300
       * @param timerId an optional key, defaults to func
       * 
       * FROM: https://stackoverflow.com/questions/27787768/debounce-function-in-jquery
       */
      if(typeof callFunctionOnce !== 'function') {
        function callFunctionOnce(func, within = 300, timerId = null) {
          window.callOnceTimers = window.callOnceTimers || {};
          timerId = timerId || func;
          if (window.callOnceTimers[timerId]) {
            clearTimeout(window.callOnceTimers[timerId]);
          }
          window.callOnceTimers[timerId] = setTimeout(func, within);
        }
      }

      function refreshDatatablesOnFilterChange(url)
      {
        // replace the datatables ajax url with new_url and reload it
        crud.table.ajax.url(url).load();
      }


      function normalizeAmpersand(string) {
        return string.replace(/&amp;/g, "&").replace(/amp%3B/g, "");
      }

      // button to remove all filters
      jQuery(document).ready(function($) {
      	$("#remove_filters_button").click(function(e) {
      		e.preventDefault();

		    	// behaviour for ajax table
		    	var new_url = '{{ url($crud->route.'/search') }}';
		    	var ajax_table = $("#crudTable").DataTable();

  				// replace the datatables ajax url with new_url and reload it
  				ajax_table.ajax.url(new_url).load();

  				// clear all filters
  				$(".navbar-filters li[filter-name]").trigger('filter:clear');

          // remove filters from URL
          crud.updateUrl(new_url);
      	});

        // hide the Remove filters button when no filter is active
        $(".navbar-filters li[filter-name]").on('filter:clear', function() {
          var anyActiveFilters = false;
          $(".navbar-filters li[filter-name]").each(function () {
            if ($(this).hasClass('active')) {
              anyActiveFilters = true;
              // console.log('ACTIVE FILTER');
            }
          });

          if (anyActiveFilters == false) {
            $('#remove_filters_button').addClass('invisible');
          }
        });
      });
    </script>
@endpush
