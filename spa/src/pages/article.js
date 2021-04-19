import {h} from 'preact';
import {findComments} from '../api/api';
import {useState, useEffect} from 'preact/hooks';

function Comment({comments}) {
    if (comments !== null && comments.length === 0) {
        return <div className="text-center pt-4">No comments yet</div>;
    }

    if (!comments) {
        return <div className="text-center pt-4">Loading...</div>;
    }

    return (
        <div className="pt-4">
            {comments.map(comment => (
                <div className="shadow border rounded-lg p-3 mb-4">
                    <div className="comment-img mr-3">
                        {!(comment._embedded && comment._embedded.photo) ? '' : (
                            <a href="#" target="_blank">
                                <img src={comment._embedded.photo} />
                            </a>
                        )}
                    </div>

                    <h5 className="font-weight-light mt-3 mb-0">{comment.author}</h5>
                    <div className="comment-text">{comment.text}</div>
                </div>
            ))}
        </div>
    );
}

export default function Article({articles, id}) {
    const article = articles.find(article => article.id === id);
    const [comments, setComments] = useState(null);

    useEffect(() => {
        findComments(article).then(comments => setComments(comments));
    }, [id]);

    return (
        <div className="p-3">
            <div className="comment-img mr-3">
                {!(article._embedded && article._embedded.cover) ? '' : (
                    <img src={article._embedded.cover} />
                )}
            </div>
            <h4>{article.title}</h4>
            <div className="comment-text" dangerouslySetInnerHTML={{__html: article.content}} />
            <Comment comments={comments} />
        </div>
    );
};