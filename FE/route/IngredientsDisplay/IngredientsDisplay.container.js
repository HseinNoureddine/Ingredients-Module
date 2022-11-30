/* eslint-disable no-restricted-globals */
import PropTypes from 'prop-types';
import { PureComponent } from 'react';
import { connect } from 'react-redux';

import { prepareQuery } from 'Util/Query';
import { executeGet } from 'Util/Request';
import { ONE_MONTH_IN_SECONDS } from 'Util/Request/QueryDispatcher';

import IngredientsQuery from '../../query/Ingredients.query';
import { PAGESIZE } from './IngredientDisplay.config';
import IngredientsDisplayComponent from './IngredientsDisplay.component';

/** @namespace Scandipwa/Route/IngredientsDisplay/Container */

export const BreadcrumbsDispatcher = import(
    /* webpackMode: "lazy", webpackChunkName: "dispatchers" */
    'Store/Breadcrumbs/Breadcrumbs.dispatcher'
);
/** @namespace Scandipwa/Route/IngredientsDisplay/Container/mapStateToProps */
export const mapStateToProps = (state) => ({
    isOffline: state.OfflineReducer.isOffline
});
/** @namespace Scandipwa/Route/IngredientsDisplay/Container/mapDispatchToProps */
export const mapDispatchToProps = (dispatch) => ({
    updateBreadcrumbs: (breadcrumbs) => (
        BreadcrumbsDispatcher.then(
            ({ default: dispatcher }) => dispatcher.update(breadcrumbs, dispatch)
        )
    )
});
/** @namespace Scandipwa/Route/IngredientsDisplay/Container */
export class IngredientsDisplayContainer extends PureComponent {
    static propTypes = {
        updateBreadcrumbs: PropTypes.func.isRequired,
        location: PropTypes.string.isRequired
    };

    componentDidMount() {
        this.getIngredients();
        this.betBaseUrl();
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
        const { match: { params: { id } } } = props;
        const { match } = props;
        const { location: { search } } = props;
        const pageNumber = search?.split('?page=')[1] || 1;
        this.state = {
            match,
            id,
            data: {},
            pageNumber
        };
        this.updateBreadcrumbs();
    }

    updateBreadcrumbs() {
        const { updateBreadcrumbs } = this.props;
        const { id } = this.state;
        const breadcrumbs = [{ url: '/ingredient', name: id },
            { url: '/ingredients', name: 'ingredients' }];

        updateBreadcrumbs(breadcrumbs);
    }

    async getIngredients(pageSize = PAGESIZE) {
        const { id, pageNumber } = this.state;
        const query = IngredientsQuery.getIngredient(id, pageSize, pageNumber);
        const CACHE_TTL = ONE_MONTH_IN_SECONDS;
        const data = executeGet(prepareQuery(query), 'Ingredient', CACHE_TTL).then(
            /** @namespace Scandipwa/Route/IngredientsDisplay/Container/IngredientsDisplayContainer/getIngredients/data/then/then/executeGet/then */
            (data) => data,
            /** @namespace Scandipwa/Route/IngredientsDisplay/Container/IngredientsDisplayContainer/getIngredients/data/then/then/executeGet/then/catch */
            (error) => error
        ).then(
            /** @namespace Scandipwa/Route/IngredientsDisplay/Container/IngredientsDisplayContainer/getIngredients/data/then/then */
            (data) => this.setState({ data })
        );

        return data;
    }

    async betBaseUrl() {
        const query = IngredientsQuery.getBaseUrl();
        const CACHE_TTL = ONE_MONTH_IN_SECONDS;
        const data = executeGet(prepareQuery(query), 'BaseUrl', CACHE_TTL).then(
            /** @namespace Scandipwa/Route/IngredientsDisplay/Container/IngredientsDisplayContainer/betBaseUrl/data/then/then/executeGet/then */
            (data) => data,
            /** @namespace Scandipwa/Route/IngredientsDisplay/Container/IngredientsDisplayContainer/betBaseUrl/data/then/then/executeGet/then/catch */
            (error) => error
        ).then(
            /** @namespace Scandipwa/Route/IngredientsDisplay/Container/IngredientsDisplayContainer/betBaseUrl/data/then/then */
            (ingredientCollection) => this.setState({ ingredientCollection })
        );

        return data;
    }

    containerProps() {
        const { data: { ingredient } } = this.state;
        const { ingredientCollection } = this.state;
        if (ingredient === undefined || ingredientCollection === undefined) {
            return {};
        }
        const { ingredientCollection: { baseUrl } } = ingredientCollection;
        const cleanUrl = baseUrl
            .replace('ingredients/ingredient/', '')
            .replace('lv/', '')
            .replace('de/', '')
            .replace('ru/', '')
            .replace('en/', '');

        return { ingredient, cleanUrl, baseUrl };
    }

    render() {
        return (
            <IngredientsDisplayComponent
              { ...this.containerProps() }
            />
        );
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(IngredientsDisplayContainer);
