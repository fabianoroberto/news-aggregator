function fetchCollection(path) {
    return fetch(ENV_API_ENDPOINT + path).then(resp => resp.json()).then(json => json['_embedded']['items']);
}

export function findArticles() {
    return fetchCollection('v1/public/articles');
}

export function findComments(article) {
    return fetchCollection(`v1/public/articles/${article.id}/comments`);
}