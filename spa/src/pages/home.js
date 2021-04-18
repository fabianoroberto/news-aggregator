import {h} from 'preact';
import {Link} from 'preact-router';

export default function Home({articles}) {
    if (!articles) {
        return <div className="p-3 text-center">No articles yet</div>;
    }

    return (
        <div className="p-3">
            {articles.map((article)=> (
                <div className="card border shadow-sm lift mb-3">
                    <div className="card-body">
                        <div className="card-title">
                            <h4 className="font-weight-light">
                                {article.title}
                            </h4>
                            <p>
                                {article.content}
                            </p>
                        </div>

                        <Link className="btn btn-sm btn-blue stretched-link" href={'/article/'+article.id}>
                            View
                        </Link>
                    </div>
                </div>
            ))}
        </div>
    );
};