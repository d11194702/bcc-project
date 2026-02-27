/**
 * Функционал поиска товаров
 */

document.addEventListener( 'DOMContentLoaded', function() {
	const searchInput = document.querySelector( '.search input[type="text"]' );
	const searchClear = document.querySelector( '.search .clear' );
	const searchResultWrap = document.querySelector( '.search-result__wrap' );
	const searchResultList = document.querySelector( '.search-result' );
	const searchMoreLink = document.querySelector( '.more-link a' );
	const searchContainer = document.querySelector( '.search' );

	if ( ! searchInput || ! searchResultWrap ) {
		return;
	}

	// Функция для отправки поиска
	function performSearch( query ) {
		if ( query.length < 2 ) {
			searchResultList.innerHTML = '';
			searchResultWrap.classList.remove( 'active' );
			return;
		}

		// AJAX запрос
		fetch( ajaxurl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: 'action=bcc_search&query=' + encodeURIComponent( query ) + '&nonce=' + bcc_search_nonce
		} )
		.then( response => response.json() )
		.then( data => {
			if ( data.success && data.data.items.length > 0 ) {
				// Очищаем список
				searchResultList.innerHTML = '';

				// Добавляем результаты
				data.data.items.forEach( item => {
					const li = document.createElement( 'li' );
					li.className = 'search-result__card';

					const thumbnail = item.thumbnail ? item.thumbnail : bccDefaultImage;
					const price = item.price ? '<span>' + item.price + '</span>' : '';

					li.innerHTML = `
						<div class="search-result__card-left">
							<img src="${thumbnail}" alt="${item.title}" class="main-img">
							<h3><b>${item.title}</b></h3>
						</div>
						<div class="search-result__card-right">
							${price}
							<a href="${item.link}">Купить</a>
						</div>
					`;

					searchResultList.appendChild( li );
				} );

				// Обновляем ссылку на полные результаты
				if ( searchMoreLink ) {
					const fullSearchUrl = window.location.origin + '/?s=' + encodeURIComponent( query );
					searchMoreLink.href = fullSearchUrl;
				}

				searchResultWrap.classList.add( 'active' );
			} else {
				searchResultList.innerHTML = '<li style="padding: 20px; text-align: center;">Ничего не найдено</li>';
				searchResultWrap.classList.add( 'active' );
			}
		} )
		.catch( error => {
			console.error( 'Ошибка поиска:', error );
		} );
	}

	// Слушаем ввод в поле поиска
	searchInput.addEventListener( 'input', function() {
		const query = this.value.trim();
		performSearch( query );
	} );

	// Кнопка очистки
	searchClear.addEventListener( 'click', function() {
		searchInput.value = '';
		searchResultList.innerHTML = '';
		searchResultWrap.classList.remove( 'active' );
		searchInput.focus();
	} );

	// Закрываем поиск при клике вне его
	document.addEventListener( 'click', function( e ) {
		if ( ! searchContainer.contains( e.target ) ) {
			searchResultWrap.classList.remove( 'active' );
		}
	} );

	// При фокусе на input показываем результаты если они есть
	searchInput.addEventListener( 'focus', function() {
		if ( this.value.trim().length >= 2 && searchResultList.innerHTML.trim() !== '' ) {
			searchResultWrap.classList.add( 'active' );
		}
	} );
} );
