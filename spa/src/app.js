import {h, render} from 'preact';
import {Router, Link} from 'preact-router';
import {useState, useEffect} from 'preact/hooks';

import {findArticles} from './api/api';
import '../assets/styles/app.scss';

import Home from "./pages/home";
import Article from "./pages/article";

function App() {
    const [articles, setArticles] = useState(null);

    useEffect(() => {
        findArticles().then((articles) => setArticles(articles));
    }, []);

    if (articles === null) {
        return <div className="text-center pt-5">Loading...</div>;
    }

    return (
        <div>
            <header className="header">
                <nav className="navbar navbar-light bg-light">
                    <div className="container">
                        <Link className="navbar-brand mr-4 pr-2" href="/">
                            &#128217; NewsAggregator
                        </Link>
                    </div>
                </nav>
            </header>

            <Router>
                <Home path="/" articles={articles} />
                <Article path="/article/:id" articles={articles} />
            </Router>
        </div>
    )
}

render(<App/>, document.getElementById('app'));