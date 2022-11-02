/* eslint-disable max-lines */
/* eslint-disable react/jsx-no-bind */
/* eslint-disable @scandipwa/scandipwa-guidelines/no-jsx-variables */
/* eslint-disable max-len */
/* eslint-disable @scandipwa/scandipwa-guidelines/jsx-no-props-destruction */
import { lazy } from 'react';
import { Route } from 'react-router-dom';

import {
    PRINT_ALL_INVOICES,
    PRINT_ALL_REFUNDS,
    PRINT_ALL_SHIPMENT,
    PRINT_INVOICE,
    PRINT_ORDER as PRINT_ORDER_REQUEST,
    PRINT_REFUND,
    PRINT_SHIPMENT
} from 'Component/MyAccountOrderPrint/MyAccountOrderPrint.config';
import UrlRewrites from 'Route/UrlRewrites';
import {
    Breadcrumbs,
    CartPage,
    Checkout,
    CmsPage,
    ConfirmAccountPage,
    ContactPage,
    CookiePopup,
    CreateAccountPage,
    DemoNotice,
    Footer,
    ForgotPasswordPage,
    Header,
    HomePage,
    LoginAccountPage,
    MenuPage,
    MyAccount,
    NavigationTabs,
    NewVersionPopup,
    NotificationList,
    OfflineNotice,
    OrderPrintPage,
    PasswordChangePage,
    ProductComparePage,
    Router as SourceRouter,
    SearchPage,
    SendConfirmationPage,
    SomethingWentWrong,
    StyleGuidePage,
    WishlistShared,
    withStoreRegex
} from 'SourceComponent/Router/Router.component';
import {
    ADDRESS_BOOK, MY_DOWNLOADABLE, MY_ORDERS, MY_WISHLIST, NEWSLETTER_SUBSCRIPTION
} from 'Type/Account.type';

import {
    ACCOUNT_FORGOT_PASSWORD,
    CART,
    CHANGE_PASSWORD,
    CHECKOUT,
    CMS_PAGE,
    COMPARE,
    CONFIRM_ACCOUNT,
    CONTACT_PAGE,
    CREATE_ACCOUNT,
    HOME,
    INGREDIENT,
    INGREDIENTS,
    LOGIN,
    MENU,
    MY_ACCOUNT,
    MY_ACCOUNT_ADDRESS,
    MY_ACCOUNT_DOWNLOADABLE,
    MY_ACCOUNT_NEWSLETTER,
    MY_ACCOUNT_ORDER,
    MY_ACCOUNT_ORDERS,
    MY_ACCOUNT_WISHLIST,
    PRINT_ORDER,
    SEARCH,
    SHARED_WISHLIST,
    STYLE_GUIDE,
    SWITCH_ITEMS_TYPE,
    URL_REWRITES
} from './Router.config';

import './Router.style';

export const Ingredients = lazy(() => import(/* webpackMode: "lazy", webpackChunkName: "cms" */ 'Route/Ingredients'));
export const IngredientsDisplay = lazy(() => import(/* webpackMode: "lazy", webpackChunkName: "cms" */ 'Route/IngredientsDisplay'));

export {
    CartPage,
    Checkout,
    CmsPage,
    CookiePopup,
    DemoNotice,
    Header,
    HomePage,
    MyAccount,
    PasswordChangePage,
    SearchPage,
    SendConfirmationPage,
    ConfirmAccountPage,
    MenuPage,
    Footer,
    NavigationTabs,
    NewVersionPopup,
    NotificationList,
    WishlistShared,
    OfflineNotice,
    ContactPage,
    ProductComparePage,
    CreateAccountPage,
    LoginAccountPage,
    ForgotPasswordPage,
    SomethingWentWrong,
    StyleGuidePage,
    Breadcrumbs,
    OrderPrintPage,
    withStoreRegex
};

/** @namespace Scandipwa/Component/Router/Component */
export class RouterComponent extends SourceRouter {
    [SWITCH_ITEMS_TYPE] = [
        {
            component: <Route path={ withStoreRegex('/') } exact render={ (props) => <HomePage { ...props } /> } />,
            position: 10,
            name: HOME
        },
        {
            component: <Route path={ withStoreRegex('/ingredients/:filter?') } exact render={ (props) => <Ingredients { ...props } /> } />,
            position: 15,
            name: INGREDIENTS
        },
        {
            component: <Route path={ withStoreRegex('/ingredients/ingredient/:id?') } exact render={ (props) => <IngredientsDisplay { ...props } /> } />,
            position: 20,
            name: INGREDIENT
        },
        {
            component: <Route path={ withStoreRegex('/search/:query/') } render={ (props) => <SearchPage { ...props } /> } />,
            position: 25,
            name: SEARCH
        },
        {
            component: <Route path={ withStoreRegex('/page') } render={ (props) => <CmsPage { ...props } /> } />,
            position: 40,
            name: CMS_PAGE
        },
        {
            component: <Route path={ withStoreRegex('/cart') } exact render={ (props) => <CartPage { ...props } /> } />,
            position: 50,
            name: CART
        },
        {
            component: <Route path={ withStoreRegex('/checkout/:step?') } render={ (props) => <Checkout { ...props } /> } />,
            position: 55,
            name: CHECKOUT
        },
        {
            component: <Route path={ withStoreRegex('/customer/account/createPassword/') } render={ (props) => <PasswordChangePage { ...props } /> } />,
            position: 60,
            name: CHANGE_PASSWORD
        },
        {
            component: <Route path={ withStoreRegex('/customer/account/create/') } render={ (props) => <CreateAccountPage { ...props } /> } />,
            position: 61,
            name: CREATE_ACCOUNT
        },
        {
            component: <Route path={ withStoreRegex('/customer/account/login/') } render={ (props) => <LoginAccountPage { ...props } /> } />,
            position: 62,
            name: LOGIN
        },
        {
            component: <Route path={ withStoreRegex('/customer/account/forgotpassword/') } render={ (props) => <ForgotPasswordPage { ...props } /> } />,
            position: 63,
            name: ACCOUNT_FORGOT_PASSWORD
        },
        {
            component: <Route path={ withStoreRegex('/customer/account/confirmation') } render={ (props) => <SendConfirmationPage { ...props } /> } />,
            position: 64,
            name: CONFIRM_ACCOUNT
        },
        {
            component: <Route path={ withStoreRegex('/customer/account/confirm') } render={ (props) => <ConfirmAccountPage { ...props } /> } />,
            position: 65,
            name: CONFIRM_ACCOUNT
        },
        {
            component: <Route path={ withStoreRegex('/sales/order/view/order_id/:orderId?') } render={ (props) => <MyAccount { ...props } selectedTab={ MY_ORDERS } /> } />,
            position: 70,
            name: MY_ACCOUNT_ORDER
        },
        {
            component: <Route path={ withStoreRegex('/sales/order/history') } render={ (props) => <MyAccount { ...props } selectedTab={ MY_ORDERS } /> } />,
            position: 71,
            name: MY_ACCOUNT_ORDERS
        },
        {
            component: <Route path={ withStoreRegex('/downloadable/customer/products') } render={ (props) => <MyAccount { ...props } selectedTab={ MY_DOWNLOADABLE } /> } />,
            position: 72,
            name: MY_ACCOUNT_DOWNLOADABLE
        },
        {
            component: <Route path={ withStoreRegex('/wishlist') } render={ (props) => <MyAccount { ...props } selectedTab={ MY_WISHLIST } /> } />,
            position: 73,
            name: MY_ACCOUNT_WISHLIST
        },
        {
            component: <Route path={ withStoreRegex('/customer/address') } render={ (props) => <MyAccount { ...props } selectedTab={ ADDRESS_BOOK } /> } />,
            position: 74,
            name: MY_ACCOUNT_ADDRESS
        },
        {
            component: <Route path={ withStoreRegex('/newsletter/manage') } render={ (props) => <MyAccount { ...props } selectedTab={ NEWSLETTER_SUBSCRIPTION } /> } />,
            position: 75,
            name: MY_ACCOUNT_NEWSLETTER
        },
        {
            component: <Route path={ withStoreRegex('/customer/account/:tab?') } render={ (props) => <MyAccount { ...props } /> } />,
            position: 76,
            name: MY_ACCOUNT
        },
        {
            component: <Route path={ withStoreRegex('/menu') } render={ (props) => <MenuPage { ...props } /> } />,
            position: 80,
            name: MENU
        },
        {
            component: <Route path={ withStoreRegex('/wishlist/shared/:code') } render={ (props) => <WishlistShared { ...props } /> } />,
            position: 81,
            name: SHARED_WISHLIST
        },
        {
            component: <Route path={ withStoreRegex('/contact') } render={ (props) => <ContactPage { ...props } /> } />,
            position: 82,
            name: CONTACT_PAGE
        },
        {
            component: <Route path={ withStoreRegex('/compare') } render={ (props) => <ProductComparePage { ...props } /> } />,
            position: 83,
            name: COMPARE
        },
        {
            component: <Route path={ withStoreRegex('/styleguide') } render={ (props) => <StyleGuidePage { ...props } /> } />,
            position: 84,
            name: STYLE_GUIDE
        },
        {
            component: <Route path={ withStoreRegex('/sales/order/print/order_id/:orderId?') } render={ (props) => <OrderPrintPage { ...props } orderPrintRequest={ PRINT_ORDER_REQUEST } /> } />,
            position: 90,
            name: PRINT_ORDER
        },
        {
            component: <Route path={ withStoreRegex('/sales/order/printInvoice/order_id/:orderId?') } render={ (props) => <OrderPrintPage { ...props } orderPrintRequest={ PRINT_ALL_INVOICES } /> } />,
            position: 91,
            name: PRINT_ORDER
        },
        {
            component: <Route path={ withStoreRegex('/sales/order/printShipment/order_id/:orderId?') } render={ (props) => <OrderPrintPage { ...props } orderPrintRequest={ PRINT_ALL_SHIPMENT } /> } />,
            position: 92,
            name: PRINT_ORDER
        },
        {
            component: <Route path={ withStoreRegex('/sales/order/printCreditmemo/order_id/:orderId?') } render={ (props) => <OrderPrintPage { ...props } orderPrintRequest={ PRINT_ALL_REFUNDS } /> } />,
            position: 93,
            name: PRINT_ORDER
        },
        {
            component: <Route path={ withStoreRegex('/sales/order/printInvoice/invoice_id/:invoiceId?') } render={ (props) => <OrderPrintPage { ...props } orderPrintRequest={ PRINT_INVOICE } /> } />,
            position: 94,
            name: PRINT_ORDER
        },
        {
            component: <Route path={ withStoreRegex('/sales/order/printShipment/shipment_id/:shipmentId?') } render={ (props) => <OrderPrintPage { ...props } orderPrintRequest={ PRINT_SHIPMENT } /> } />,
            position: 95,
            name: PRINT_ORDER
        },
        {
            component: <Route path={ withStoreRegex('/sales/order/printCreditmemo/creditmemo_id/:refundId?') } render={ (props) => <OrderPrintPage { ...props } orderPrintRequest={ PRINT_REFUND } /> } />,
            position: 95,
            name: PRINT_ORDER
        },
        {
            component: <Route render={ (props) => <UrlRewrites { ...props } /> } />,
            position: 1000,
            name: URL_REWRITES
        }
    ];
}

export default RouterComponent;
