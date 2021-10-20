'use strict';

const getType = Object.prototype.toString;

// 判空
export function isEmpty(o) {
	if (o == null || o === undefined || o === '') {
		return true;
	}
	if (isBoolean(o)) {
		return !o;
	}
	if (isNumber(o)) {
		return !!(o === 0 || isNaN(o));
	}
	if (isArrayLike(o) && (isArray(o) || isString(o))) {
		return o.length === 0;
	}

	return Object.keys(o).length === 0;
}

// 数组
export function isArray(o) {
	const str = getType.call(o);
	return str === '[object Array]';
}

// 类数组
function isArrayLike(o) {
	if (o && // o is not null, undefined, etc.
		typeof o === 'object' && // o is an object
		isFinite(o.length) && // o.length is a finite number
		o.length >= 0 && // o.length is non-negative
		o.length === Math.floor(o.length) && // o.length is an integer
		o.length < 4294967296) { // o.length < 2^32
		return true; // Then o is array-like
	} else {
		return false; // Otherwise it is not
	}
}

// 字符串
export function isString(o) {
	const str = getType.call(o);
	return str === '[object String]';
}

// 数字(包含整型和浮点)
export function isNumber(o) {
	const str = getType.call(o);
	return str === '[object Number]';
}

// 整型
export function isInteger(o) {
	const isNumber = isNumber(o);
	const cal = o % 1;
	return isNumber && cal === 0;
};

// 布尔值
export function isBoolean(o) {
	const str = getType.call(o);
	return str === '[object Boolean]';
};

// 对象
export function isObject(o) {
	const str = getType.call(o);
	return str === '[object Object]';
};

// 方法
export function isFunction(o) {
	const str = getType.call(o);
	return str === '[object Function]';
};

//判断是否为手机号的正则表达式
export function isPhoneNum(phones) {
	var myreg = /^[1][3,4,5,7,8,9][0-9]{9}$/;
	if (!myreg.test(phones)) {
		return false;
	} else {
		return true;
	}
}

/**
 * 判断是否为正确的身份证号码
 * @param {String} idCard 身份证号码
 */
export function isIdCard(idCard) {
	const reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
	return reg.test(idCard)
}

/**
 * 是否为银行卡号码
 * @param {String} bankCount
 */
export function isBankCount(bankCount) {
	const regExp = /^([1-9]{1})(\d{15}|\d{18})$/; 
	return regExp.test(bankCount) 
} 


/**
 * @description  验证是否是图片
 */
export function isImage(file) {
	const regExp = /\.(png|jpg|gif|jpeg|webp)$/; 
	return regExp.test(file) 
}

/**
 * @description  验证是否pdf
 */
export function isPdf(file) {
	const regExp = /\.pdf$/; 
	return regExp.test(file) 
}

/**
 * @description  验证是否邮箱
 */
export function isEmail(str) {
  const regExp = /^[A-Za-zd0-9]+([-_.][A-Za-zd]+)*@([A-Za-zd]+[-.])+[A-Za-zd]{2,5}$/;
  return regExp.test(str);
}