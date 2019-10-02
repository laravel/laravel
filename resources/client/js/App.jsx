import React from 'react'
import Title from './components/Title'

class App extends React.Component {
    render() {
        return (
            <div className="content">
                <div className="title m-b-md">
                    <Title text="Laravel"/>
                </div>

                <div className="links">
                    <a href="https://laravel.com/docs">Docs</a>
                    <a href="https://laracasts.com">Laracasts</a>
                    <a href="https://laravel-news.com">News</a>
                    <a href="https://blog.laravel.com">Blog</a>
                    <a href="https://nova.laravel.com">Nova</a>
                    <a href="https://forge.laravel.com">Forge</a>
                    <a href="https://vapor.laravel.com">Vapor</a>
                    <a href="https://github.com/laravel/laravel">GitHub</a>
                </div>
            </div>
        )
    }
}

export default <App />
