export default class Te {

    // TODO: Use other Template Engine soon
    render(html, options) {

        html = html.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');

        const re = /<%([^%>]+)?%>/g
        const reExp = /(^( )?(if|for|else|switch|case|break|{|}))(.*)?/g
        let code = 'var r=[];\n' + 'var v=' + JSON.stringify(options) + ';\n'
        let cursor = 0;
        let blabla;

        const add = function (line, js) {
            js ? (code += line.match(reExp) ? line + '\n' : 'r.push(' + line + ');\n') :
                (code += line != '' ? 'r.push("' + line.replace(/"/g, '\\"') + '");\n' : '');
            return add;
        }

        while ((blabla = re.exec(html)) !== null) {
            add(html.slice(cursor, blabla.index))(blabla[1], true);
            cursor = blabla.index + blabla[0].length;
        }

        add(html.substr(cursor, html.length - cursor));

        code += 'return r.join("");';

        return new Function(code.replace(/[\r\t\n]/g, '')).apply(options);

    }

}