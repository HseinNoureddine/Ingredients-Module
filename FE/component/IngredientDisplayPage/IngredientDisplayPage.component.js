import PropTypes from 'prop-types';
import { PureComponent } from 'react';

import Pagination from 'Component/Pagination';

import Html from '../../component/Html';
import Image from '../../component/Image';
import Link from '../../component/Link';

import './IngredientDisplayPage.style';

/** @namespace Scandipwa/Component/IngredientDisplayPage/Component */
export class IngredientDisplayPageComponent extends PureComponent {
    static propTypes = {
        ingredient: PropTypes.arrayOf(PropTypes.string),
        cleanUrl: PropTypes.string
    };

    static defaultProps = {
        ingredient: [],
        cleanUrl: ''
    };

    render() {
        const { ingredient, cleanUrl } = this.props;
        const { numberOfProductPages } = ingredient;
        const { description } = ingredient;
        if (ingredient.length === 0) {
            return (<h1>loading...</h1>);
        }

        function data() {
            return (
                <>
                <h3>related products</h3>
                <div block="IngredientDisplayPage" elem="Grid">
                    <ol block="Grid" elem="Ol">
                { ingredient.products.map((item, i) => {
                    const keyValue = i + 1;
                    return (

                <li block="Ol" elem="Item" key={ keyValue }>
                <div block="Item" elem="Info">
                    <Link
                      href={ item.url }
                      to={ item.url }
                      block="Info"
                      elem="Link"
                    >
                        <Image src={ `${cleanUrl}media/catalog/product/${ item.img}` } />
                    </Link>
                    <div block="Item" elem="Product">
                        <strong block="Product" elem="Name">
                            <Link
                              block="Name"
                              elem="Link"
                              title={ item.name }
                              href={ item.url }
                              to={ item.url }
                            >
                                { item.name }
                            </Link>
                        </strong>
                    </div>
                </div>
                </li>

                    );
                }) }
                    </ol>
                </div>
                </>
            );
        }
        function ingredientDisplay() {
            return (
            <div block="IngredientDisplayPage" elem="Block">
                <p>Ingredients</p>
                <h1 block="Block" elem="Title">{ ingredient.name }</h1>
                <Image
                  block="Block"
                  elem="Image"
                  src={ `${cleanUrl}media/ingredients/${ ingredient.img}` }
                  width="200"
                  height="200"
                />
                <div block="Block" elem="Description">
                    <br />
                    <Html content={ description } />
                </div>
            </div>
            );
        }

        return (
        <div block="IngredientDisplayPage">
            { ingredientDisplay() }
            { data() }
            <Pagination totalPages={ numberOfProductPages } />
        </div>
        );
    }
}

export default IngredientDisplayPageComponent;
