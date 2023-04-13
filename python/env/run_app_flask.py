import numpy as np
import networkx as nx
import matplotlib.pyplot as plt
import json
from flask_mysqldb import MySQL
import base64
import time
from flask import Flask, request,Response
from flask_cors import CORS
import io
import matplotlib
from tabulate import tabulate
matplotlib.use('Agg')


app = Flask(__name__)
CORS(app)
app.config['MYSQL_HOST'] = 'localhost'  # ganti dengan host dari MySQL Anda
app.config['MYSQL_USER'] = 'root'  # ganti dengan username MySQL Anda
app.config['MYSQL_PASSWORD'] = ''  # ganti dengan password MySQL Anda
# ganti dengan nama database yang ingin Anda gunakan
app.config['MYSQL_DB'] = 'project_TA'
app.config['MYSQL_CURSORCLASS'] = 'DictCursor'
mysql = MySQL(app)


@app.route('/create_data_processing_table')
def create_graphimage_table():
    cur = mysql.connection.cursor()
    cur.execute("CREATE TABLE data_graph (id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, nama_project VARCHAR(255) NOT NULL, base64code LONGTEXT NOT NULL)")
    cur.execute("CREATE TABLE data_rank (id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, nama_project VARCHAR(255) NOT NULL, json LONGTEXT NOT NULL)")
    mysql.connection.commit()
    cur.close()
    return "Tabel berhasil dibuat!"


def query_graph(nama_project, base64code):
    cur = mysql.connection.cursor()
    cur.execute("INSERT INTO data_graph (nama_project, base64code) VALUES (%s, %s)",
                (nama_project, base64code))
    mysql.connection.commit()
    cur.close()
    return "Data berhasil disimpan!"


def query_rank(nama_project, json):
    cur = mysql.connection.cursor()
    cur.execute(
        "INSERT INTO data_rank (nama_project, json) VALUES (%s, %s)", (nama_project, json))
    mysql.connection.commit()
    cur.close()
    return "Data berhasil disimpan!"


def getData(data=None):
    if data == None:
        table = [
            ["a1", ['a', 'b', 'c'],   ['a', 'b', 'c', 'k', 'l'], '1993', ['p1', 'p2'],'title of a1','nation of p1'], 
            ["a2", ['c', 'd', 'e'],   ['a', 'c', 'd', 'e', 'm', 'n'], '1993', ['p1', 'p3'],'title of a2','nation of p1'],
            ["a3", ['f', 'g', 'h'],   ['c', 'd', 'f', 'g', 'h', 'o'], '1993', ['p2', 'p4', 'p5'],'title of a3','nation of p2'], 
            ["a4", ['i', 'j'],        ['c', 'd', 'p', 'q'], '1994', ['p3', 'p6'], ['a1', 'a2'], 'title of a4', 'nation of p3'], 
            ["a5", ['dj', 'dk'],      ['a', 'dj', 'dk', 'm', 'r'], '1994', ['p1', 'p7'], ['a1', 'a2', 'a3'], 'title of a5','nation of p1'], 
            ["a6", ['d', 'ac', 'ad'], ['d', 'ac', 'ad', 's', 't'], '1994', ['p8', 'p9'], ['a1', 'a3'], 'title of a6','nation of p8'],
        ]
    else:
        table = data
    return (table)


def getArticleIdAuthorReferencesAndAuthor(table):
    pairs = []
    authors = []
    articles = []
    initial_articles_pair = []
    title_articles_pair = []
    initial_author_pair = []
    nation_author_pair = []

    for i in table:
        row = []
        row.append(i[0])
        articles.append(i[0])
        row.append(i[4])
        count=0
        for penulis in i[4]:
            count+=1
            if count==1:
                initial_author_pair.append(penulis)
                print("this is my "+i[len(i)-1])
                nation_author_pair.append(i[len(i)-1])
            authors.append(penulis)
        try:
            row.append(i[5])
            for article in i[5]:
                # memastikan article != ''
                if len(article) > 1:
                    articles.append(article)
        except:
            row.append([])
        pairs.append(row)

        # memasukkan array kode artikel dan judulnya
        initial_articles_pair.append(i[0])
        title_articles_pair.append(i[len(i)-2])
    # menghilangkan duplikat
    authors = sorted(set(authors))
    articles = sorted(set(articles))
    return pairs, authors, articles,initial_articles_pair ,title_articles_pair,initial_author_pair,nation_author_pair


def author_matrixs(authors):
    author_matrix = []
    for author_x in authors:
        for author_y in authors:
            row = []
            row.append(author_x)
            row.append(author_y)
            author_matrix.append(row)
    return author_matrix


def getTable2Data(pairs, search_matrix, type):
    author_matrixs = []
    for i in search_matrix:
        author_matrixs.append([i[0], i[1], 0])

    print("getTable2Data")
    if type == "author":
        for i in pairs:
            penulisList = i[1]
            authorList = i[2]
            for author in authorList:
                # try:
                row_author = []
                for row in pairs:
                    if author == row[0]:
                        for author2 in row[1]:
                            row_author.append(author2)
                        # skip karena sudah ketemu
                        break

                for author in penulisList:
                    for row in row_author:
                        if author != row:
                            index = search_matrix.index([author, row])
                            author_matrixs[index][2] += 1
                print("\n")
    elif type == "article":
        for i in pairs:
            penulisList = i[0]
            authorList = i[2]
            author = penulisList
            for author_reference in authorList:
                # memastikan article/author != ''
                if len(author) <= 1 or len(author_reference) <= 1:
                    continue
                index = search_matrix.index([author, author_reference])
                print("index:"+str(index)+"author"+str(author)+"author_reference"+str(author_reference))
                author_matrixs[index][2] += 1

    return author_matrixs


def index_2d(myList, v):
    for i, x in enumerate(myList):
        if v in x:
            return i  # , x.index(v)


def makeTable2(author_matrix, authors):
    import pandas as pd
    pretable2 = []
    for x in authors:
        authortmp = []
        for y in author_matrix:
            if y[1] == x:
                try:
                    authortmp.append(y[2])
                except:
                    authortmp.append(0)
        pretable2.append(authortmp)
    # print(pretable2)
    table2 = pd.DataFrame(pretable2, columns=authors, index=authors)
    print("tabel 2")
    print(table2)
    return table2, pretable2


def getTopAuthor(authors, author_rank, ranking):
    author_ranking = []
    count = -1
    for author in authors:
        count += 1
        author_ranking.append((author, author_rank[count]))
    sorted_authors = sorted(author_ranking, key=lambda x: x[1], reverse=True)
    # get the top 20 author names
    top_authors = [x[0] for x in sorted_authors[:ranking]]
    return top_authors


def add_node_graph(G, author_matrixs):
    for author_matrix in author_matrixs:
        if author_matrix[2] > 0:
            # (penulis merujuk,dirujuk,nilai)
            G.add_edge(author_matrix[0],
                       author_matrix[1], weight=author_matrix[2])
            G.add_node(author_matrix[0])
            G.add_node(author_matrix[1])
    return G


def get_no_outer_author(authors, author_rank, exist_authors):
    count = -1
    outer_author_rank = []
    outer_authors = []
    for author in authors:
        count += 1
        if author not in exist_authors:
            outer_author_rank.append(author_rank[count])
            outer_authors.append(author)
            authors.pop(count)
            author_rank.pop(count)
    return authors, author_rank, outer_author_rank, outer_authors


def makeTermGraph(authors, author_matrixs, author_rank, outer_author, ranking):
    # acuan
    search_author = []
    for i in authors:
        search_author.append(i)

    # nilai author tanpa hubungan ex:0.123
    rank_outer_author = author_rank[len(author_rank)-1]
    # ranking author yang ingin ditampilkan ex:5,10,20
    ranking = ranking
    # pilihan menampilkan author tanpa relasi ex:tampilkan,tidak
    outer_author = outer_author
    # dapatkan list top author ex:['p1','p2','p3']
    top_authors = getTopAuthor(authors, author_rank, ranking)
    # inisilaize graph
    G = nx.Graph()
    # author merujuk & dirujuk
    G = add_node_graph(G, author_matrixs)
    # inisiliasisi ukuran node dan warna
    my_node_sizes = []
    my_node_colors = []
    labels = {}

    print(len(authors))
    print(len(author_rank))

    authors, author_rank, outer_author_rank, outer_authors = get_no_outer_author(
        authors, author_rank, G.nodes)

    print(len(authors))
    print(len(author_rank))

    for author in G.nodes:
        size = author_rank[authors.index(author)]
        if size > rank_outer_author:
            # jika iya nilainya *300
            my_node_sizes.append(size * 800)
            if author in top_authors:
                my_node_colors.append('purple')
            else:
                my_node_colors.append('blue')
        else:
            # jika tidak dirujuk nilainya 10
            my_node_sizes.append(1000)
            my_node_colors.append('red')
        labels[author] = str(search_author.index(author))

    if outer_author == True:
        for author, size in zip(outer_authors, outer_author_rank):
            G.add_node(author)
            my_node_sizes.append(100)
            my_node_colors.append('red')
            labels[author] = str(search_author.index(author))

    # default=125
    total_author = len(G.nodes)

    subplot_size = total_author/5
    k = subplot_size/60

    fig, ax = plt.subplots(figsize=(25, 25))
    # decrease k parameter to increase spacing between nodes
    pos = nx.spring_layout(G, seed=7, k=0.4)
    nx.draw_networkx_nodes(G, pos, alpha=0.7,
                           node_size=my_node_sizes,
                           node_color=my_node_colors
                           )  # increase node size to 200
    nx.draw_networkx_edges(G, pos, edgelist=G.edges(),
                           width=1, alpha=0.5, edge_color="b")
    nx.draw_networkx_labels(G, pos, font_size=8,
                            font_family="sans-serif", font_color="black",
                            labels=labels
                            )

    edge_labels = nx.get_edge_attributes(G, name='weight')
    edge_labels = {(u, v): weight_matrix for u, v,
                   weight_matrix in G.edges(data='weight')}
    nx.draw_networkx_edge_labels(G, pos, edge_labels, font_size=5)
    buf = io.BytesIO()
    plt.savefig(buf, format='png')

    output = buf
    output.seek(0)
    my_base64_jpgData = base64.b64encode(output.read())
    # query_graph("project 1",my_base64_jpgData)

    return buf


def makeTermGraph2(authors, author_matrixs, author_rank, outer_author, ranking):
    rank_outer_author = author_rank[len(author_rank)-1]
    G = nx.Graph()
    labels = {}
    my_node_sizes = []
    my_node_colors = []
    my_node_label_color = []

    author_ranking = []
    count = -1
    for author in authors:
        count += 1
        author_ranking.append((author, author_rank[count]))

    sorted_authors = sorted(author_ranking, key=lambda x: x[1], reverse=True)

    # get the top 20 author names
    top_authors = [x[0] for x in sorted_authors[:ranking]]

    count = -1
    # Add nodes to the graph
    for author, size in zip(authors, author_rank):
        count += 1
        if size > rank_outer_author:
            G.add_node(author)
            # jika iya nilainya *300
            my_node_sizes.append(size * 300)
            if author in top_authors:
                my_node_colors.append('purple')
            else:
                my_node_colors.append('blue')
            labels[author] = author
        else:
            G.add_node(author)
            # jika tidak dirujuk nilainya 10
            if outer_author == True:
                my_node_sizes.append(8)
                my_node_label_color.append(8)
                labels[author] = author
            else:
                my_node_sizes.append(0)
                labels[author] = ""
            my_node_colors.append('red')

    for author_matrix in author_matrixs:
        if author_matrix[2] > 0:
            # print("value:"+str(author_matrix[2]))
            # jika ada hubungan dengan top author maka tambahkan edge
            if outer_author == False:
                if (author_matrix[0] in top_authors or author_matrix[1] in top_authors):
                    G.add_edge(
                        author_matrix[0], author_matrix[1], weight=author_matrix[2])
                    print(
                        "edge:"+str(author_matrix[0])+"-"+str(author_matrix[1]))
            else:
                G.add_edge(
                    author_matrix[0], author_matrix[1], weight=author_matrix[2])
                print("edge:"+str(author_matrix[0])+"-"+str(author_matrix[1]))

            index = authors.index(author_matrix[0])
            if my_node_sizes[index] == 8 or my_node_sizes[index] == 0:
                # node yang merujuk tapi tidak dirujuk ubah size=100
                my_node_sizes[index] = 100
                labels[authors[index]] = authors[index]
    # Draw the graph
    # fig, ax = plt.subplots(figsize=(15,12)) # increase plot size to 10x8 inches
    # increase plot size to 10x8 inches
    fig, ax = plt.subplots(figsize=(90, 72))
    # decrease k parameter to increase spacing between nodes
    pos = nx.spring_layout(G, seed=7, k=0.4)
    nx.draw_networkx_nodes(G, pos, node_size=my_node_sizes, alpha=0.7,
                           node_color=my_node_colors)  # increase node size to 200
    nx.draw_networkx_edges(G, pos, edgelist=G.edges(),
                           width=1, alpha=0.5, edge_color="b")
    nx.draw_networkx_labels(G, pos, labels, font_size=8,
                            font_family="sans-serif", font_color="black")

    edge_labels = nx.get_edge_attributes(G, name='weight')
    edge_labels = {(u, v): weight_matrix for u, v,
                   weight_matrix in G.edges(data='weight')}
    nx.draw_networkx_edge_labels(G, pos, edge_labels, font_size=5)
    buf = io.BytesIO()
    plt.savefig(buf, format='png')

    output = buf
    output.seek(0)
    my_base64_jpgData = base64.b64encode(output.read())
    # query_graph("project 1",my_base64_jpgData)

    return buf


def addTable2TotalRowAndColoumn(pretable2, authors):
    import pandas as pd
    sumrow = []
    sumcol = []
    lenauthor = len(authors)
    for x in range(lenauthor):
        nilai = 0
        for y in range(lenauthor):
            nilai = nilai+pretable2[x][y]
        sumrow.append(nilai)
    print("p1p9")
    print(sumrow)

    sumcol = []
    for x in range(lenauthor):
        nilai = 0
        for y in range(lenauthor):
            nilai = nilai+pretable2[y][x]
        sumcol.append(nilai)
    sumcol.append(0)
    print("p9p1")
    print(sumcol)
    for x in range(lenauthor):
        pretable2[x].append(sumrow[x])
    pretable2.append(sumcol)
    print(pretable2)
    print("tabel 3: Add Total Row & Col")
    table2 = pd.DataFrame(pretable2)
    print(table2)
    return pretable2


def makeNewAdjMatrix(pretable3, lenauthor):
    import pandas as pd
    for x in range(lenauthor):
        for y in range(lenauthor):
            if pretable3[lenauthor][y] == 0:
                # print("nilaiku="+str(pretable3[x][y]))
                pretable3[x][y] = 1/lenauthor
            else:
                # print("nilaiku="+str(pretable3[x][y]))
                pretable3[x][y] = pretable3[x][y]/pretable3[lenauthor][y]
    table3 = pd.DataFrame(pretable3)
    print("tabel 3:new adj Matrix")
    print(table3)
    return pretable3


def rank(pretable3, author, name):
    import numpy as np
    import pandas as pd
    lenauthor = len(author)
    d = 0.850466963
    table4 = []
    row = []
    for x in range(lenauthor):
        row.append(1/lenauthor)
    table4.append(row)
    for y in range(100):
        rowbaru = []
        for x in range(lenauthor):
            nilai = (1-d)+d * \
                np.matmul(pretable3[x][0:lenauthor], row[0:lenauthor])
            rowbaru.append(nilai)
        table4.append(rowbaru)
        selisih = abs(np.array(row)-np.array(rowbaru))
        ns = max(selisih)
        if ns < 0.001:
            break
        # print(ns)
        row = rowbaru
    rank = [sorted(row, reverse=True).index(x) for x in row]
    rank = [x + 1 for x in rank]
    table4.append(rank)
    table5 = pd.DataFrame(table4)
    print("tabel 3: Ranking")
    print(table5.T)

    json_data = json.dumps({"author": author, "ranks": rank})
    # query_rank("project 1",json_data)
    return table4, rank,rowbaru


@app.route('/data/<type>/<name>', methods=['GET', 'POST'])
def data(type, name):
    if request.method == 'POST' or request.method == 'GET':
        start_time = time.time()
        if request.method == 'POST':
            table = getData(request.get_json()["data"])
        elif request.method == 'GET':
            table = getData()

        print("Tabel 1")
        title = ['Article-ID', 'Terms in Title and Keywords',
                 'Terms Found in Abstracts', 'Publication Year', 'Authors', 'References']
        print(title)
        print(tabulate(table))

    # pair ArticleId,Author,& References & author
        pairs, authors, articles,initial_articles_pair ,title_articles_pair,initial_author_pair,nation_author_pair = getArticleIdAuthorReferencesAndAuthor(table)

        # for i in pairs:
        #     print(i)
        #     print("\n")
        # for y in authors:
        #     print(y)
        #     print("\n")

        # pasangan yang memungkinkan antara 2 penulis
        if type == "article":
            input_author_article = articles
        elif type == "author":
            input_author_article = authors
        author_matrix = author_matrixs(input_author_article)

    # ambil data untuk tabel 2(step 1)
        author_matrix_and_relation = getTable2Data(pairs, author_matrix, type)

        # for x in author_matrix_and_relation:
        #     print(x)
        # return author_matrix_and_relation

    # errornyadisini
        table2, raw_table2 = makeTable2(
            author_matrix_and_relation, input_author_article)
        # add total coloum & row in table 2
        raw_table2WithRowCol = addTable2TotalRowAndColoumn(
            raw_table2, input_author_article)
        # makeNewAdjMatrix
        newAdjMatrixs = makeNewAdjMatrix(
            raw_table2WithRowCol, len(input_author_article))
        # rank author
        table, author_rank,last_author_rank = rank(newAdjMatrixs, input_author_article, name)

        try:
            outer_author = request.get_json()["outer"]
            top_author_rank = request.get_json()["author-rank"]
        except:
            outer_author = True
            top_author_rank = 10

        if name == "graph":
            # Make Term Graph
            output = makeTermGraph(
                input_author_article, author_matrix_and_relation, last_author_rank, outer_author, top_author_rank)
            output.seek(0)
            my_base64_jpgData = base64.b64encode(output.read())
            if request.method == 'GET':
                end_time = time.time()
                total_time = end_time - start_time
                print(
                    "Waktu eksekusi program: {:.2f} detik".format(total_time))
                return Response(output.getvalue(), mimetype='image/png')
            else:
                end_time = time.time()
                total_time = end_time - start_time
                print(
                    "Waktu eksekusi program: {:.2f} detik".format(total_time))
                return my_base64_jpgData
        elif name == "rank":
            title_nation_of_the_article = []
            for i in input_author_article:
                if type == "article":
                    title_nation_of_the_article.append(title_articles_pair[initial_articles_pair.index(i)])
                elif type == "author":
                    try:
                        title_nation_of_the_article.append(nation_author_pair[initial_author_pair.index(i)])
                    except:
                        # bukan penulis pertama
                        title_nation_of_the_article.append("None")
            tmp = [input_author_article, [table, author_rank]]
            if type == "article" or type == "author":
                tmp.append(title_nation_of_the_article)
            end_time = time.time()
            total_time = end_time - start_time
            print("Waktu eksekusi program: {:.2f} detik".format(total_time))
            return tmp
        
        elif name == "rankgraph":
            title_nation_of_the_article = []
            for i in input_author_article:
                if type == "article":
                    title_nation_of_the_article.append(title_articles_pair[initial_articles_pair.index(i)])
                elif type == "author":
                    try:
                        title_nation_of_the_article.append(nation_author_pair[initial_author_pair.index(i)])
                    except:
                        # bukan penulis pertama
                        title_nation_of_the_article.append("None")

            tmp = {'authors':input_author_article, 'ranks':author_rank,'title':title_nation_of_the_article}
            tmp=json.dumps(tmp)
            # Make Term Graph
            output = makeTermGraph(input_author_article, author_matrix_and_relation, last_author_rank, outer_author, top_author_rank)
            output.seek(0)
            my_base64_jpgData = base64.b64encode(output.read())
            my_base64_jpgData=my_base64_jpgData.decode("utf-8")
            tmp_dict = json.loads(tmp)
            tmp_dict['graph']=my_base64_jpgData

            end_time = time.time()
            total_time = end_time - start_time
            print("Waktu eksekusi program: {:.2f} detik".format(total_time))
            return tmp_dict
        

if __name__ == "__main__":
    app.run(debug=True)
