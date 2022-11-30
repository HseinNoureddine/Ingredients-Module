import PropTypes from 'prop-types';
import { PureComponent } from 'react';

import IngredientDisplayPage from '../../component/IngredientDisplayPage';
/** @namespace Scandipwa/Route/IngredientsDisplay/Component */
export class IngredientsDisplayComponent extends PureComponent {
    static propTypes = {
        ingredient: PropTypes.arrayOf(PropTypes.string).isRequired,
        cleanUrl: PropTypes.string.isRequired,
        baseUrl: PropTypes.string.isRequired
    };

    render() {
        const { ingredient, cleanUrl, baseUrl } = this.props;
        return (
            <IngredientDisplayPage ingredient={ ingredient } cleanUrl={ cleanUrl } baseUrl={ baseUrl } />
        );
    }
}

export default IngredientsDisplayComponent;
