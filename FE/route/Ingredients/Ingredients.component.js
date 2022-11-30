import PropTypes from 'prop-types';
import { PureComponent } from 'react';

import IngredientsList from '../../component/IngredientsList';

/** @namespace Scandipwa/Route/Ingredients/Component */
export class IngredientsComponent extends PureComponent {
    static propTypes = {
        filtered: PropTypes.arrayOf(PropTypes.arrayOf(PropTypes.string)).isRequired,
        letter: PropTypes.arrayOf(PropTypes.string).isRequired,
        categories: PropTypes.arrayOf(PropTypes.arrayOf(PropTypes.string)).isRequired,
        cleanUrl: PropTypes.string.isRequired,
        baseUrl: PropTypes.string.isRequired,
        numberOfPages: PropTypes.number.isRequired,
        isPagination: PropTypes.bool.isRequired
    };

    render() {
        const {
            categories, letter, filtered, cleanUrl, numberOfPages, isPagination, baseUrl
        } = this.props;

        return (
            <IngredientsList
              items={ filtered }
              letters={ letter }
              categories={ categories }
              cleanUrl={ cleanUrl }
              baseUrl={ baseUrl }
              numberOfPages={ numberOfPages }
              isPagination={ isPagination }
            />
        );
    }
}

export default IngredientsComponent;
