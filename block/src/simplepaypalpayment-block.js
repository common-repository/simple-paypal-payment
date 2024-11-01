const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { SelectControl, TextControl, PanelBody } = wp.components;
const { serverSideRender: ServerSideRender } = wp;

registerBlockType(
	'simple-paypal-payment/simplepaypalpayment-block',
	{
		title: 'Simple PayPal Payment',
		icon: 'tickets-alt',
		category: 'widgets',

		edit ( props ) {

			return [
			<Fragment>
				<div>{ simplepaypalpayment_text.button_attention }</div>
				<ServerSideRender
					block = 'simple-paypal-payment/simplepaypalpayment-block'
					attributes = { props.attributes }
				/>
				<div id="simple_paypal_payment_paypal-button"></div>
				<TextControl
					label = { simplepaypalpayment_text.amount }
					value = { props.attributes.amount }
					onChange = { ( value ) => props.setAttributes( { amount: value } ) }
				/>
				<SelectControl
					label = { simplepaypalpayment_text.locale }
					value = { props.attributes.locale }
					options = { simplepaypalpayment_locale_codes }
					onChange = { ( value ) => props.setAttributes( { locale: value } ) }
				/>
				<SelectControl
					label = { simplepaypalpayment_text.currency }
					value = { props.attributes.currency }
					options = { simplepaypalpayment_currency_codes }
					onChange = { ( value ) => props.setAttributes( { currency: value } ) }
				/>
				<div>{ simplepaypalpayment_text.check }</div>

				<InspectorControls>
				{}
					<PanelBody title = { simplepaypalpayment_text.amount } initialOpen = { false }>
						<TextControl
							label = { simplepaypalpayment_text.amount }
							value = { props.attributes.amount }
							onChange = { ( value ) => props.setAttributes( { amount: value } ) }
						/>
					</PanelBody>
					<PanelBody title = { simplepaypalpayment_text.codes } initialOpen = { false }>
						<SelectControl
							label = { simplepaypalpayment_text.locale }
							value = { props.attributes.locale }
							options = { simplepaypalpayment_locale_codes }
							onChange = { ( value ) => props.setAttributes( { locale: value } ) }
						/>
						<SelectControl
							label = { simplepaypalpayment_text.currency }
							value = { props.attributes.currency }
							options = { simplepaypalpayment_currency_codes }
							onChange = { ( value ) => props.setAttributes( { currency: value } ) }
						/>
					</PanelBody>
					<PanelBody title = { simplepaypalpayment_text.button } initialOpen = { false }>
						<SelectControl
							label = { 'size' }
							value = { props.attributes.size }
							options = { [
							{ value: 'small', label: 'small' },
							{ value: 'medium', label: 'medium' },
							{ value: 'large', label: 'large' },
							{ value: 'responsive', label: 'responsive' },
							] }
							onChange = { ( value ) => props.setAttributes( { size: value } ) }
						/>
						<SelectControl
							label = { 'color' }
							value = { props.attributes.color }
							options = { [
							{ value: 'gold', label: 'gold' },
							{ value: 'blue', label: 'blue' },
							{ value: 'silver', label: 'silver' },
							{ value: 'white', label: 'white' },
							{ value: 'black', label: 'black' },
							] }
							onChange = { ( value ) => props.setAttributes( { color: value } ) }
						/>
						<SelectControl
							label = { 'shape' }
							value = { props.attributes.shape }
							options = { [
							{ value: 'pill', label: 'pill' },
							{ value: 'rect', label: 'rect' },
							] }
							onChange = { ( value ) => props.setAttributes( { shape: value } ) }
						/>
						<SelectControl
							label = { 'label' }
							value = { props.attributes.label }
							options = { [
							{ value: 'checkout', label: 'checkout' },
							{ value: 'credit', label: 'credit' },
							{ value: 'pay', label: 'pay' },
							{ value: 'buynow', label: 'buynow' },
							{ value: 'paypal', label: 'paypal' },
							] }
							onChange = { ( value ) => props.setAttributes( { label: value } ) }
						/>
					</PanelBody>
					<PanelBody title = { simplepaypalpayment_text.view } initialOpen = { false }>
						<TextControl
							label = { simplepaypalpayment_text.before }
							value = { props.attributes.before }
							onChange = { ( value ) => props.setAttributes( { before: value } ) }
						/>
						<TextControl
							label = { simplepaypalpayment_text.after }
							value = { props.attributes.after }
							onChange = { ( value ) => props.setAttributes( { after: value } ) }
						/>
						<TextControl
							label = { simplepaypalpayment_text.remove }
							value = { props.attributes.remove }
							onChange = { ( value ) => props.setAttributes( { remove: value } ) }
						/>
					</PanelBody>
					<PanelBody title = { simplepaypalpayment_text.payname } initialOpen = { false }>
						<TextControl
							label = { simplepaypalpayment_text.payname }
							value = { props.attributes.payname }
							onChange = { ( value ) => props.setAttributes( { payname: value } ) }
						/>
					</PanelBody>
				</InspectorControls>
			</Fragment>
			];
		},

		save () {
			return
			<Fragment>
				<span class="simple_paypal_payment_before">{ props.attributes.before }</span><span class="simple_paypal_payment_after"></span><div id="simple_paypal_payment_paypal-button"></div>
			</Fragment>
		},

	}
);
