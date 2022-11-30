import PropTypes from 'prop-types';
import { PureComponent } from 'react';
import { connect } from 'react-redux';

import { prepareQuery } from 'Util/Query';
import { executeGet } from 'Util/Request';
import { ONE_MONTH_IN_SECONDS } from 'Util/Request/QueryDispatcher';

import IngredientsQuery from '../../query/Ingredients.query';
import IngredientsComponent from './Ingredients.component';
import {
    MAX, PAGESIZE
} from './Ingredients.config';

export const BreadcrumbsDispatcher = import(
    /* webpackMode: "lazy", webpackChunkName: "dispatchers" */
    'Store/Breadcrumbs/Breadcrumbs.dispatcher'
);
/** @namespace Scandipwa/Route/Ingredients/Container/mapStateToProps */
export const mapStateToProps = (state) => ({
    isOffline: state.OfflineReducer.isOffline
});
/** @namespace Scandipwa/Route/Ingredients/Container/mapDispatchToProps */
export const mapDispatchToProps = (dispatch) => ({
    updateBreadcrumbs: (breadcrumbs) => (
        BreadcrumbsDispatcher.then(
            ({ default: dispatcher }) => dispatcher.update(breadcrumbs, dispatch)
        )
    )
});
/** @namespace Scandipwa/Route/Ingredients/Container */
export class IngredientsContainer extends PureComponent {
    static propTypes = {
        updateBreadcrumbs: PropTypes.func.isRequired,
        location: PropTypes.string.isRequired
    };

    componentDidMount() {
        this.getIngredients();
        this.getAllingredients();
        this.getCategories();
        this.updateBreadcrumbs();
    }

    componentDidUpdate() {
        this.updateBreadcrumbs();
        const { pageNumber } = this.state;
        const { location: { search } } = this.props;
        const curPageNumber = search?.split('?page=')[1] || 1;
        if (curPageNumber !== pageNumber) {
            this.setState({ pageNumber: curPageNumber }, this.getIngredients());
        }
    }

    __construct(props) {
        super.__construct(props);
        const { match: { params: { filter } } } = props;
        const { location: { search } } = props;
        const pageNumber = search?.split('?page=')[1] || 1;
        this.state = {
            data: {},
            cats: {},
            filter,
            pageNumber
        };
        this.updateBreadcrumbs();
    }

    updateBreadcrumbs() {
        const { updateBreadcrumbs } = this.props;
        const { filter } = this.state;
        if (filter !== undefined) {
            updateBreadcrumbs([{ url: '/ingredient', name: filter },
                { url: '/ingredients', name: 'ingredients' }]);
        } else {
            updateBreadcrumbs([{ url: '/ingredients', name: 'ingredients' }]);
        }
    }

    async getIngredients(pageSize = PAGESIZE) {
        const { pageNumber } = this.state;
        const query = IngredientsQuery.getIngredientCollection(pageNumber, pageSize);
        const CACHE_TTL = ONE_MONTH_IN_SECONDS;
        const data = executeGet(prepareQuery(query), 'Ingredients', CACHE_TTL).then(
            /** @namespace Scandipwa/Route/Ingredients/Container/IngredientsContainer/getIngredients/data/then/then/executeGet/then */
            (data) => data,
            /** @namespace Scandipwa/Route/Ingredients/Container/IngredientsContainer/getIngredients/data/then/then/executeGet/then/catch */
            (error) => error
        ).then(
            /** @namespace Scandipwa/Route/Ingredients/Container/IngredientsContainer/getIngredients/data/then/then */
            (data) => {
                this.setState({ data });
            }
        );

        return data;
    }

    async getAllingredients(pageSize = MAX) {
        const query = IngredientsQuery.getIngredientCollection(1, pageSize);
        const CACHE_TTL = ONE_MONTH_IN_SECONDS;
        const data = executeGet(prepareQuery(query), 'AllIngredients', CACHE_TTL).then(
            /** @namespace Scandipwa/Route/Ingredients/Container/IngredientsContainer/getAllingredients/data/then/then/executeGet/then */
            (data) => data,
            /** @namespace Scandipwa/Route/Ingredients/Container/IngredientsContainer/getAllingredients/data/then/then/executeGet/then/catch */
            (error) => error
        ).then(
            /** @namespace Scandipwa/Route/Ingredients/Container/IngredientsContainer/getAllingredients/data/then/then */
            (allIngredients) => {
                this.setState({ allIngredients });
            }
        );

        return data;
    }

    async getCategories() {
        const query = IngredientsQuery.getCategoryCollection();
        const CACHE_TTL = ONE_MONTH_IN_SECONDS;
        const data = executeGet(prepareQuery(query), 'IngredientCategories', CACHE_TTL).then(
            /** @namespace Scandipwa/Route/Ingredients/Container/IngredientsContainer/getCategories/data/then/then/executeGet/then */
            (data) => data,
            /** @namespace Scandipwa/Route/Ingredients/Container/IngredientsContainer/getCategories/data/then/then/executeGet/then/catch */
            (error) => error
        ).then(
            /** @namespace Scandipwa/Route/Ingredients/Container/IngredientsContainer/getCategories/data/then/then */
            (cats) => {
                this.setState({ cats });
            }
        );

        return data;
    }

    filterIngredients(filter, filtered, allIng, categories) {
        if (filter !== undefined) {
            if (filter.length === 1) {
                return allIng.filter((e) => e.letter === filter);
            }
            const c = categories.filter((e) => e.name === filter);
            if (c.length !== 0) {
                const { id } = c[0];
                return allIng.filter((e) => e.categoryID === id);
            }
        }

        return filtered;
    }

    containerProps() {
        const {
            data, cats, filter, allIngredients
        } = this.state;

        if (data.ingredientCollection === undefined || cats.categoryCollection === undefined) {
            return 'empty';
        }
        const { categoryCollection: { categories } } = cats;
        const { ingredientCollection: { ingredients, baseUrl, numberOfPages } } = data;
        const allIng = allIngredients.ingredientCollection.ingredients;
        const filtered = this.filterIngredients(filter, ingredients, allIng, categories);
        const letters = allIng.map((e) => e.letter);
        const sortedLetters = letters.sort();
        // unique sorted letters
        const letter = Array.from(new Set(sortedLetters));
        const isPagination = filter === undefined;
        const cleanUrl = baseUrl
            .replace('lv/', '')
            .replace('de/', '')
            .replace('ru/', '')
            .replace('en/', '');

        return {
            filtered, letter, categories, cleanUrl, numberOfPages, isPagination, baseUrl
        };
    }

    render() {
        return (
            <IngredientsComponent
              { ...this.containerProps() }
              { ...this.containerFunctions }
            />
        );
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(IngredientsContainer);
